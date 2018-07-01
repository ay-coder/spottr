<?php
namespace App\Http\Transformers;

use App\Http\Transformers;
use URL;


class PostsTransformer extends Transformer
{
    /**
     * Transform
     *
     * @param array $data
     * @return array
     */
    public function transform($item)
    {
        if(is_array($item))
        {
            $item = (object)$item;
        }

        $item->tag_user = (object)$item->tag_user;

        return [
            "post_id"       => (int) $item->id, 
            "user_id"       => (int) $item->user_id,
            "tag_user_id"   => (int) $item->tag_user_id,
            "media"         =>  URL::to('/').'/uploads/media/' . $item->media, 
            "description"   =>  $item->description,
            "is_image"      => (int) $item->is_image,
            "is_video"      => (int) $item->is_video,
            'name'          => $this->nulltoBlank($item->tag_user->name),
            'email'         => $this->nulltoBlank($item->tag_user->email),
            'phone'         => $this->nulltoBlank($item->tag_user->phone),
            'viewCount'     => (int) 50,
            'profile_pic'   => isset($item->tag_user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->tag_user->profile_pic : '',
         ];
    }

    public function getUserPosts($user, $items)
    {
        $userReadPostIds    = [];
        $userReadPosts      = access()->getReadPostIds($user->id);

        if($userReadPosts)
        {   
            $userReadPostIds = array_values($userReadPosts->unique()->toArray());
        }

        $response = [];

        foreach($items as $item)
        {
            $item->user = (object)$item->user;
            $isRead     = in_array($item->id, $userReadPostIds) ? 1 :0;

            if($isRead == 0) 
            {
                $response['unread'][] = [
                    "post_id"       => (int) $item->id, 
                    "user_id"       => (int) $item->user_id,
                    "tag_user_id"   => (int) $item->tag_user_id,
                    "media"         =>  URL::to('/').'/uploads/media/' . $item->media, 
                    "description"   =>  $item->description,
                    "is_image"      => (int) $item->is_image,
                    "is_video"      => (int) $item->is_video,
                    'name'          => $this->nulltoBlank($item->user->name),
                    'email'         => $this->nulltoBlank($item->user->email),
                    'phone'         => $this->nulltoBlank($item->user->phone),
                    'viewCount'     => (int) 50,
                    'profile_pic'   => isset($item->user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->user->profile_pic : '',
                    'is_read'       => 0
                ];
            }
            else
            {
                $response['read'][] = [
                    "post_id"       => (int) $item->id, 
                    "user_id"       => (int) $item->user_id,
                    "tag_user_id"   => (int) $item->tag_user_id,
                    "media"         =>  URL::to('/').'/uploads/media/' . $item->media, 
                    "description"   =>  $item->description,
                    "is_image"      => (int) $item->is_image,
                    "is_video"      => (int) $item->is_video,
                    'name'          => $this->nulltoBlank($item->user->name),
                    'email'         => $this->nulltoBlank($item->user->email),
                    'phone'         => $this->nulltoBlank($item->user->phone),
                    'viewCount'     => (int) 50,
                    'profile_pic'   => isset($item->user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->user->profile_pic : '',
                    'is_read'       => 1
                ];
            }
        }

        return $response;
    }

    public function singlePost($item)
    {
        $item->user = (object)$item->user;

        $response = [
            "post_id"       => (int) $item->id, 
            "user_id"       => (int) $item->user_id,
            "tag_user_id"   => (int) $item->tag_user_id,
            "media"         =>  URL::to('/').'/uploads/media/' . $item->media, 
            "description"   =>  $item->description,
            "is_image"      => (int) $item->is_image,
            "is_video"      => (int) $item->is_video,
            'name'          => $this->nulltoBlank($item->user->name),
            'email'         => $this->nulltoBlank($item->user->email),
            'phone'         => $this->nulltoBlank($item->user->phone),
            'profile_pic'   => isset($item->user->profile_pic) ? URL::to('/').'/uploads/user/' . $item->user->profile_pic : '',
            'is_read'       => 1,
            'viewCount'     => (int) 50,
            'view_count'    => isset($item->views) ? count($item->views) : 0,
            'comments_count' => isset($item->comments) ? count($item->comments) : 0,
            'comments'      => []
        ];

        if(isset($item->comments) && count($item->comments))
        {
            $commentData = [];

            foreach($item->comments as $comment)
            {
                $commentData[] = [
                    'user_id'       => $comment->user_id,
                    'name'          => $comment->user->name,
                    'profile_pic'   => isset($comment->user->profile_pic) ? URL::to('/').'/uploads/user/' . $comment->user->profile_pic : '',
                    'comment'       => $comment->comment
                ];
            }
        }

        $response['comments'] = $commentData;

        return $response;

    }
}