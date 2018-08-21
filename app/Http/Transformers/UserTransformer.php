<?php

namespace App\Http\Transformers;

use URL;
use App\Http\Transformers;

class UserTransformer extends Transformer 
{
    public function transform($data) 
    {
        return [
            'user_id'       => $data->id,
            'token'         => $this->nulltoBlank($data->token),
            'device_token'  => $data->device_token,
            'name'          => $this->nulltoBlank($data->name),
            'email'         => $this->nulltoBlank($data->email),
            'phone'         => $this->nulltoBlank($data->phone),
            'profile_pic'   => isset($data->profile_pic) ? URL::to('/').'/uploads/user/' . $data->profile_pic : '',
            'dob'           => $this->nulltoBlank($data->dob),
            'gender'        => $this->nulltoBlank($data->gender),
            'bio'           => $this->nulltoBlank($data->bio),
            'description'   => 'Lorem Ipusm Lorem Ipsum description',
            'connectionCount' => isset($data->connections) ? count($data->connections) : 0, 
            'postCount'     => isset($data->connections) ? count($data->connections) : 0, 
            'notification_count' => (int) 0
        ];
    }
    
    public function userInfo($data)
    {
        $data = (object) $data;
        $postCount         = isset($data->posts) ? count($data->posts) : 0;
        $notificationCount = isset($data->notifications) ? count($data->notifications) : 0;
        $myConnections     = isset($data->my_connections) ? count($data->my_connections) : 0;

        $accConnections     = isset($data->accepted_connections) ? count($data->accepted_connections) : 0;
        
        $connectionCount =  $myConnections + $accConnections;
        
        return [
            'user_id'       => $data->id,
            'token'         => isset($data->token) ? $this->nulltoBlank($data->token) : '',
            'device_token'  => $data->device_token,
            'name'          => $this->nulltoBlank($data->name),
            'email'         => $this->nulltoBlank($data->email),
            'phone'         => $this->nulltoBlank($data->phone),
            'profile_pic'   => isset($data->profile_pic) ? URL::to('/').'/uploads/user/' . $data->profile_pic : '',
            'dob'           => $this->nulltoBlank($data->dob),
            'gender'        => $this->nulltoBlank($data->gender),
            'bio'           => $this->nulltoBlank($data->bio),
            'description'   => 'Lorem Ipusm Lorem Ipsum description',
            'connectionCount'   => (int) $connectionCount, 
            'postCount'             => (int) $postCount, 
            'notification_count' => (int) $notificationCount,
            'is_connected'      => $data->is_connected,
            'is_same_user'      => $data->is_same_user,
            'show_connect_btn'  => $data->show_connect_btn
        ];
    }

    public function getUserInfo($data) 
    {
        return [
            'userId'    => $data->id,
            'name'      => $this->nulltoBlank($data->name),
            'email'     => $this->nulltoBlank($data->email)
        ];
    }
    
    /**
     * userDetail
     * Single user detail
     * 
     * @param type $data
     * @return type
     */
    public function userDetail($data) {
        return [
            'UserId' => isset($data['id']) ? $data['id'] : "",
            'QuickBlocksId' => isset($data['quick_blocks_id']) ? $data['quick_blocks_id'] : "",
            'MobileNumber' => isset($data['mobile_number']) ? $data['mobile_number'] : "",
            'Name' => isset($data['username']) ? $data['username'] : "",
            'Specialty' => isset($data['specialty']) ? $data['specialty'] : "",
            'ProfilePhoto' => isset($data['profile_photo'])?$this->getUserImage($data['profile_photo']):""
        ];
    }

    /*
     * User Detail and it's parameters
     */
    public function singleUserDetail($data){        
        return [
            'UserId' => $data['id'],            
            'Name' => $this->nulltoBlank($data['name']),
            'Email' => $this->nulltoBlank($data['email']),
            'MobileNumber' => $this->nulltoBlank($data['mobile_number']),
        ];
    }
    
    public function transformStateCollection(array $items) {
        return array_map([$this, 'getState'], $items);

    }

    /**
     * Update User
     * 
     * @param object $data
     * @return array
     */
    public function updateUser($data)
    {
        $headerToken = request()->header('Authorization');
        $userToken   = '';

        if($headerToken)
        {
            $token      = explode(" ", $headerToken);
            $userToken  = $token[1];
        }

        return [
            'user_id'       => $data->id,
            'token'         => $this->nulltoBlank($data->token),
            'device_token'  => $data->device_token,
            'name'          => $this->nulltoBlank($data->name),
            'email'         => $this->nulltoBlank($data->email),
            'phone'         => $this->nulltoBlank($data->phone),
            'profile_pic'   => isset($data->profile_pic) ? URL::to('/').'/uploads/user/' . $data->profile_pic : '',
            'dob'           => $this->nulltoBlank($data->dob),
            'gender'        => $this->nulltoBlank($data->gender),
            'bio'           => $this->nulltoBlank($data->bio),
            'description'   => 'Lorem Ipusm Lorem Ipsum description',
            'connectionCount' => isset($data->connections) ? count($data->connections) : 0, 
            'postCount'     => isset($data->connections) ? count($data->connections) : 0, 
            'notification_count' => (int) 0
        ]; 
    }
}
