<?php
namespace App\Models;
use App\Models\CommonModel;

class ChatModel extends CommonModel
{

    private $pusher_api_key = [];
    private $pusher_options = [];

    public function __construct()
    { //{{{

        parent::__construct();

        $keys = file_get_contents(ROOTPATH."/keys.json");
        $keys = json_decode($keys, true);
        $this->pusher_api_key = $keys["pusher"];

        $this->pusher_options = array(
            'cluster' => 'ap3',
            'useTLS' => true
        );

    } //}}}

    public function Send($channel, $sender_uuid, $message, $chat_type)
    { //{{{
        helper("specialchars");

        $query = "
            select
                *
            from
                ".$chat_type."
            where
                uuid = '".$sender_uuid."'
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        $query = "
            insert into
                chat
            set
                 channel_uuid = '".$channel."'
                ,sender_uuid = '".$sender_uuid."'
                ,sender_type = '".$chat_type."'
                ,message = '".specialchars($message)."'
                ,checked = 1
                ,register_date = '".date("Y-m-d H:i:s")."'
        ";
        $this->wrdb->insert($query);

        $query = "
            update
                chat_channel
            set
                update_date = '".date("Y-m-d H:i:s")."'
            where
                uuid = '".$channel."'
        ";
        $this->wrdb->update($query);


        $pusher = new \Pusher\Pusher(
             $this->pusher_api_key["key"]
            ,$this->pusher_api_key["secret"]
            ,$this->pusher_api_key["app_id"]
            ,$this->pusher_options
        );

        $data['profile_img_uuid'] = $row["profile_img_uuid"];
        $data['name'] = ($chat_type == "user")? $row["name"] : $row["company_name"];
        $data['message'] = specialchars($message);
        $pusher->trigger($channel, $chat_type, $data);

    } //}}}

    public function getChannelInfo($channel_uuid)
    { //{{{
        $query = "
            select
                *
            from
                chat_channel
            where
                uuid = '".$channel_uuid."'
        ";
        $this->rodb->query($query);
        $row = $this->rodb->next_row();

        return $row;

    } //}}}

    public function getChat($channel_uuid)
    { //{{{
        $data = [];
        $query = "
            select
                *
            from
                chat
            where
                channel_uuid = '".$channel_uuid."'
        ";
        $this->rodb->query($query);
        while($row = $this->rodb->next_row()){

            if($row["sender_type"] == "company"){
                $query1 = "
                    select
                        *
                    from
                        company
                    where
                        uuid = '".$row["sender_uuid"]."'
                ";
                $this->wrdb->query($query1);
                $row1 = $this->wrdb->next_row();
                $row["name"] = $row1["company_name"];
                $row["profile_img_uuid"] = $row1["profile_img_uuid"];
            }
            else {
                $query1 = "
                    select
                        *
                    from
                        user
                    where
                        uuid = '".$row["sender_uuid"]."'
                ";
                $this->wrdb->query($query1);
                $row1 = $this->wrdb->next_row();
                $row["name"] = $row1["name"];
                $row["profile_img_uuid"] = $row1["profile_img_uuid"];
            }


            $data[] = $row;
        }

        return $data;

    } //}}}

    public function CreateChannel($user_uuid, $company_uuid)
    { //{{{
        helper("uuid_v4");

        $query = "
            select
                uuid
            from
                chat_channel
            where
                user_uuid = '".$user_uuid."' and
                company_uuid = '".$company_uuid."'
            limit 1
        ";
        $channel_uuid = $this->rodb->simple_query($query);

        if(!$channel_uuid){
            $channel_uuid = gen_uuid_v4();
            $query = "
                insert ignore into
                    chat_channel
                set 
                     uuid = '".$channel_uuid."'
                    ,user_uuid = '".$user_uuid."'
                    ,company_uuid = '".$company_uuid."'
                    ,update_date = '".date("Y-m-d H:i:s")."'
                    ,register_date = '".date("Y-m-d H:i:s")."'
            ";
            $idx = $this->wrdb->insert($query);
        }

        return $channel_uuid;

    } //}}}



    public function Test()
    { //{{{
        $pusher = new \Pusher\Pusher(
             $this->pusher_api_key["key"]
            ,$this->pusher_api_key["secret"]
            ,$this->pusher_api_key["app_id"]
            ,$this->pusher_options
        );

        $data['message'] = 'hello world';
        $pusher->trigger('my-channel', 'my-event', $data);

    } //}}}
}
