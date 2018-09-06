<?php namespace App\Repositories\Comments;

/**
 * Class EloquentCommentsRepository
 *
 * @author Anuj Jaha ( er.anujjaha@gmail.com)
 */

use App\Models\Comments\Comments;
use App\Repositories\DbRepository;
use App\Exceptions\GeneralException;
use App\Models\Access\User\User;
use App\Models\Posts\Posts;

class EloquentCommentsRepository extends DbRepository
{
    /**
     * Comments Model
     *
     * @var Object
     */
    public $model;

    /**
     * Comments Title
     *
     * @var string
     */
    public $moduleTitle = 'Comments';

    /**
     * Table Headers
     *
     * @var array
     */
    public $tableHeaders = [
        'id'            => 'Id',
        'username'      => 'Username',
        'post'          => 'Post Title',
        'comment'       => 'Comment',
        'created_at'    => 'Created Time',
        "actions"       => "Actions"
    ];

    /**
     * Table Columns
     *
     * @var array
     */
    public $tableColumns = [
        'id' =>   [
                'data'          => 'id',
                'name'          => 'id',
                'searchable'    => true,
                'sortable'      => true
            ],
		'username' =>   [
                'data'          => 'username',
                'name'          => 'username',
                'searchable'    => true,
                'sortable'      => true
            ],
        'post' =>   [
                'data'          => 'post',
                'name'          => 'post',
                'searchable'    => true,
                'sortable'      => true
            ],
		'comment' =>   [
                'data'          => 'comment',
                'name'          => 'comment',
                'searchable'    => true,
                'sortable'      => true
            ],
		'created_at' =>   [
                'data'          => 'created_at',
                'name'          => 'created_at',
                'searchable'    => true,
                'sortable'      => true
            ],
		
		'actions' => [
            'data'          => 'actions',
            'name'          => 'actions',
            'searchable'    => false,
            'sortable'      => false
        ]
    ];

    /**
     * Is Admin
     *
     * @var boolean
     */
    protected $isAdmin = false;

    /**
     * Admin Route Prefix
     *
     * @var string
     */
    public $adminRoutePrefix = 'admin';

    /**
     * Client Route Prefix
     *
     * @var string
     */
    public $clientRoutePrefix = 'frontend';

    /**
     * Admin View Prefix
     *
     * @var string
     */
    public $adminViewPrefix = 'backend';

    /**
     * Client View Prefix
     *
     * @var string
     */
    public $clientViewPrefix = 'frontend';

    /**
     * Module Routes
     *
     * @var array
     */
    public $moduleRoutes = [
        'listRoute'     => 'comments.index',
        'createRoute'   => 'comments.create',
        'storeRoute'    => 'comments.store',
        'editRoute'     => 'comments.edit',
        'updateRoute'   => 'comments.update',
        'deleteRoute'   => 'comments.destroy',
        'dataRoute'     => 'comments.get-list-data'
    ];

    /**
     * Module Views
     *
     * @var array
     */
    public $moduleViews = [
        'listView'      => 'comments.index',
        'createView'    => 'comments.create',
        'editView'      => 'comments.edit',
        'deleteView'    => 'comments.destroy',
    ];

    /**
     * Construct
     *
     */
    public function __construct()
    {
        $this->model        = new Comments;
        $this->userModel    = new User;
        $this->postModel    = new Posts;
    }

    /**
     * Create Comments
     *
     * @param array $input
     * @return mixed
     */
    public function create($input)
    {
        $input = $this->prepareInputData($input, true);
        $model = $this->model->create($input);

        if($model)
        {
            return $model;
        }

        return false;
    }

    /**
     * Update Comments
     *
     * @param int $id
     * @param array $input
     * @return bool|int|mixed
     */
    public function update($id, $input)
    {
        $model = $this->model->find($id);

        if($model)
        {
            $input = $this->prepareInputData($input);

            return $model->update($input);
        }

        return false;
    }

    /**
     * Destroy Comments
     *
     * @param int $id
     * @return mixed
     * @throws GeneralException
     */
    public function destroy($id)
    {
        $model = $this->model->find($id);

        if($model)
        {
            return $model->delete();
        }

        return  false;
    }

    /**
     * Get All
     *
     * @param string $orderBy
     * @param string $sort
     * @return mixed
     */
    public function getAll($orderBy = 'id', $sort = 'asc')
    {
        return $this->model->orderBy($orderBy, $sort)->get();
    }

    /**
     * Get by Id
     *
     * @param int $id
     * @return mixed
     */
    public function getById($id = null)
    {
        if($id)
        {
            return $this->model->find($id);
        }

        return false;
    }

    /**
     * Get Table Fields
     *
     * @return array
     */
    public function getTableFields()
    {
        return [
            $this->model->getTable().'.*',
            $this->userModel->getTable().'.name as username',
            $this->postModel->getTable().'.description as post',
        ];
    }

    /**
     * @return mixed
     */
    public function getForDataTable()
    {
        return  $this->model->select($this->getTableFields())
                ->leftjoin($this->userModel->getTable(), $this->userModel->getTable().'.id', '=', $this->model->getTable().'.user_id')
                ->leftjoin($this->postModel->getTable(), $this->postModel->getTable().'.id', '=', $this->model->getTable().'.post_id')->get();
        //return $this->model->select($this->getTableFields())->get();
    }

    /**
     * Set Admin
     *
     * @param boolean $isAdmin [description]
     */
    public function setAdmin($isAdmin = false)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Prepare Input Data
     *
     * @param array $input
     * @param bool $isCreate
     * @return array
     */
    public function prepareInputData($input = array(), $isCreate = false)
    {
        if($isCreate)
        {
            $input = array_merge($input, ['user_id' => access()->user()->id]);
        }

        return $input;
    }

    /**
     * Get Table Headers
     *
     * @return string
     */
    public function getTableHeaders()
    {
        if($this->isAdmin)
        {
            return json_encode($this->setTableStructure($this->tableHeaders));
        }

        $clientHeaders = $this->tableHeaders;

        unset($clientHeaders['username']);

        return json_encode($this->setTableStructure($clientHeaders));
    }

    /**
     * Get Table Columns
     *
     * @return string
     */
    public function getTableColumns()
    {
        if($this->isAdmin)
        {
            return json_encode($this->setTableStructure($this->tableColumns));
        }

        $clientColumns = $this->tableColumns;

        unset($clientColumns['username']);

        return json_encode($this->setTableStructure($clientColumns));
    }
}