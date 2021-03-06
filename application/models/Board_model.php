<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Board_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function lists($kind='', $offset='', $limit='', $subject='', $content='', $category = '') {
        if($category) $this->db->where('category', $category);
        $this->db->order_by('wrt_datetime desc');
        $this->db->like('subject', $subject);
        $this->db->like('content', $content);
        $this->db->from('board a',$limit,$offset);
        $this->db->join('board_img b', ' a.idx = b.board_idx','left');

        if ($kind == 'num_rows') {
            $result=$this->db->get()->num_rows();
        } else {
            $result=$this->db->get()->result();
        }
        //echo $this->db->last_query();
        return $result;
    }


    function best_lists($kind='', $offset='0', $limit='3', $subject='', $content='') {

        $this->db->from('board a');
        $this->db->join('board_img b', ' a.idx = b.board_idx','left');
        $this->db->order_by('hits desc');
        $this->db->limit($limit);
        $result=$this->db->get()->result();
//        echo $this->db->last_query();
        return $result;
    }

    function desc($idx)
    {
        $query=$this->db->get_where('board',array('idx'=>$idx));
        $result=$query->row();
        return $result;
    }

    function board_insert($tmp_data)
    {
        $data=array(
            'subject'=>$tmp_data['subject'],
            'content'=>$tmp_data['content'],
            'wrt_datetime'=>date('Y-m-d H:i:s'),
            'category' => $tmp_data['category']
        );
        $tmp_path = explode('/www', $tmp_data['file_path']);
        $file_path = $tmp_path[1];
        $this->db->insert('board',$data);
        $wr_id = $this->db->insert_id();
        if($tmp_data['file_name']){
            $data=array(
                'board_idx'=>$wr_id,
                'file_name'=>$tmp_data['file_name'],
                'file_path'=>$file_path,
                'file_size'=>$tmp_data['file_size'],
                'image_width'=>$tmp_data['image_width'],
                'image_height'=>$tmp_data['image_height'],
                'image_type'=>$tmp_data['image_type'],
                'image_size_str' => $tmp_data['image_size_str']
            );
            $this->db->insert('board_img',$data);
        }
        return $wr_id;
    }

    function view($idx)
    {
        $query=$this->db->get_where('board',array('idx'=>$idx));
        $result=$query->row_array();
        return $result;
    }
    function update($idx, $subject, $content)
    {
        $data=array(
            'subject'=>$subject,
            'content'=>$content
        );
        $this->db->where('idx',$idx);
        $this->db->update('board',$data);
    }

    function del($idx)
    {
        $this->db->delete('board',array('idx'=>$idx));
    }

    function truncate()
    {
        $this->db->truncate('board');
    }


    function comment_insert($tmp_data)
    {
        $data=array(
            'post_id'=>$tmp_data['post_id'],
            'cmt_username'=>$tmp_data['cmt_username'],
            'cmt_img'=>$tmp_data['cmt_img'],
            'cmt_datetime'=>date('Y-m-d H:i:s'),
            'cmt_ip'=>$_SERVER['REMOTE_ADDR'],
            'cmt_content' => $tmp_data['cmt_content']
        );
        $this->db->insert('comment',$data);
        //echo $this->db->last_query();

        $ret['code'] = "success";
        return $ret;
    }

    function comment($idx)
    {
        $this->db->order_by('cmt_id desc');
        $this->db->like('post_id', $idx);
        $this->db->from('comment');

        $result=$this->db->get()->result();
        //echo $this->db->last_query();
        return $result;
    }

}