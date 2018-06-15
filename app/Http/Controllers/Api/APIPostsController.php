<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Transformers\PostsTransformer;
use App\Http\Controllers\Api\BaseApiController;
use App\Repositories\Posts\EloquentPostsRepository;
use Illuminate\Support\Facades\Validator;

class APIPostsController extends BaseApiController
{
    /**
     * Posts Transformer
     *
     * @var Object
     */
    protected $postsTransformer;

    /**
     * Repository
     *
     * @var Object
     */
    protected $repository;

    /**
     * PrimaryKey
     *
     * @var string
     */
    protected $primaryKey = 'postsId';

    /**
     * __construct
     *
     */
    public function __construct()
    {
        $this->repository                       = new EloquentPostsRepository();
        $this->postsTransformer = new PostsTransformer();
    }

    /**
     * List of All Posts
     *
     * @param Request $request
     * @return json
     */
    public function index(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $condition  = ['tag_user_id' => $userInfo->id];
        $items      = $paginate ? $this->repository->model->with(['user', 'tag_user'])->where($condition)->orderBy($orderBy, $order)->paginate($paginate)->items() : $this->repository->getAll($condition, $orderBy, $order);

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->postsTransformer->getUserPosts($userInfo, $items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Posts!'
            ], 'No Posts Found !');
    }

    /**
     * List of All Posts
     *
     * @param Request $request
     * @return json
     */
    public function my(Request $request)
    {
        $userInfo   = $this->getAuthenticatedUser();
        $paginate   = $request->get('paginate') ? $request->get('paginate') : false;
        $orderBy    = $request->get('orderBy') ? $request->get('orderBy') : 'id';
        $order      = $request->get('order') ? $request->get('order') : 'DESC';
        $condition  = ['user_id' => $userInfo->id];
        $items      = $paginate ? $this->repository->model->with(['user', 'tag_user'])->where($condition)->orderBy($orderBy, $order)->paginate($paginate)->items() : $this->repository->getAll($condition, $orderBy, $order);

        if(isset($items) && count($items))
        {
            $itemsOutput = $this->postsTransformer->getUserPosts($userInfo, $items);

            return $this->successResponse($itemsOutput);
        }

        return $this->setStatusCode(400)->failureResponse([
            'message' => 'Unable to find Posts!'
            ], 'No Posts Found !');
    }

    /**
     * Create
     *
     * @param Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_user_id'   => 'required',
            'media'         => 'required'
        ]);

        if($validator->fails()) 
        {
            $messageData = '';

            foreach($validator->messages()->toArray() as $message)
            {
                $messageData = $message[0];
            }
            return $this->failureResponse($validator->messages(), $messageData);
        }

        $input      = $request->all();
        $userInfo   = $this->getAuthenticatedUser();
        $input      = array_merge($input, [
            'is_image'  => 1, 
            'is_video'  => 0, 
            'user_id'   => $userInfo->id,
            'media'     => 'default.png'
        ]);

        if($request->file('media'))
        {
            $imageName  = rand(11111, 99999) . '_media.' . $request->file('media')->getClientOriginalExtension();
            $request->file('media')->move(base_path() . '/public/uploads/media/', $imageName);
            $input = array_merge($input, ['media' => $imageName]);

            if($request->get('is_video') && $request->get('is_video') == 1)
            {
                $input = array_merge($input, ['is_image' => 0, 'is_video' => 1]);                
            }
        }

        $model = $this->repository->create($input);

        if($model)
        {
            $responseData = $this->postsTransformer->transform($model);

            return $this->successResponse($responseData, 'Posts is Created Successfully');
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
            ], 'Something went wrong !');
    }

    /**
     * View
     *
     * @param Request $request
     * @return string
     */
    public function show(Request $request)
    {
        $itemId = (int) hasher()->decode($request->get('post_id'));

        if($itemId)
        {
            $postInfo = $this->repository->model->with(['user', 'tag_user', 'comments', 'views'])->where('id', $itemId)->first();
            
            if($postInfo)
            {
                $responseData = $this->postsTransformer->singlePost($postInfo);

                return $this->successResponse($responseData, 'View Item');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs or Item not exists !'
            ], 'Something went wrong !');
    }

    /**
     * Edit
     *
     * @param Request $request
     * @return string
     */
    public function edit(Request $request)
    {
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->update($itemId, $request->all());

            if($status)
            {
                $itemData       = $this->repository->getById($itemId);
                $responseData   = $this->postsTransformer->transform($itemData);

                return $this->successResponse($responseData, 'Posts is Edited Successfully');
            }
        }

        return $this->setStatusCode(400)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }

    /**
     * Delete
     *
     * @param Request $request
     * @return string
     */
    public function delete(Request $request)
    {
        $itemId = (int) hasher()->decode($request->get($this->primaryKey));

        if($itemId)
        {
            $status = $this->repository->destroy($itemId);

            if($status)
            {
                return $this->successResponse([
                    'success' => 'Posts Deleted'
                ], 'Posts is Deleted Successfully');
            }
        }

        return $this->setStatusCode(404)->failureResponse([
            'reason' => 'Invalid Inputs'
        ], 'Something went wrong !');
    }
}