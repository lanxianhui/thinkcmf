<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\model;

use think\Model;
use tree\Tree;

class PortalCategoryModel extends Model
{

    /**
     * 生成分类 select树形结构
     * @param int $selectId 需要选中的分类 id
     * @param int $currentCid 需要隐藏的分类 id
     * @return string
     */
    public function adminCategoryTree($selectId = 0, $currentCid = 0)
    {
        $where = ['delete_time' => 0];
        if (!empty($currentCid)) {
            $where['id'] = ['neq', $currentCid];
        }
        $categories = $this->order("list_order ASC")->where($where)->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        $newCategories = [];
        foreach ($categories as $item) {
            $item['selected'] = $selectId == $item['id'] ? "selected" : "";

            array_push($newCategories, $item);
        }

        $tree->init($newCategories);
        $str     = '<option value=\"{$id}\" {$selected}>{$spacer}{$name}</option>';
        $treeStr = $tree->getTree(0, $str);

        return $treeStr;
    }

    /**
     * @param int|array $currentIds
     * @param string $tpl
     * @return string
     */
    public function adminCategoryTableTree($currentIds = 0, $tpl = '')
    {
        $where = ['delete_time' => 0];
//        if (!empty($currentCid)) {
//            $where['id'] = ['neq', $currentCid];
//        }
        $categories = $this->order("list_order ASC")->where($where)->select()->toArray();

        $tree       = new Tree();
        $tree->icon = ['&nbsp;&nbsp;│', '&nbsp;&nbsp;├─', '&nbsp;&nbsp;└─'];
        $tree->nbsp = '&nbsp;&nbsp;';

        if (!is_array($currentIds)) {
            $currentIds = [$currentIds];
        }

        $newCategories = [];
        foreach ($categories as $item) {
            $item['checked'] = in_array($item['id'], $currentIds) ? "checked" : "";
            $item['url']     = url('portal/List/index', ['id' => $item['id']]);;
            $item['str_action'] = '<a href="' . url("AdminCategory/add", ["parent" => $item['id']]) . '">添加子分类</a> | <a href="' . url("AdminCategory/edit", ["id" => $item['id']]) . '">' . lang('EDIT') . '</a> | <a class="js-ajax-delete" href="' . url("AdminCategory/delete", ["id" => $item['id']]) . '">' . lang('DELETE') . '</a> ';
            array_push($newCategories, $item);
        }

        $tree->init($newCategories);

        if (empty($tpl)) {
            $tpl = "<tr>
					<td><input name='list_orders[\$id]' type='text' size='3' value='\$list_order' class='input-order'></td>
					<td>\$id</td>
					<td>\$spacer <a href='\$url' target='_blank'>\$name</a></td>
					<td>\$description</td>
					<td>\$str_action</td>
				</tr>";
        }
        $treeStr = $tree->getTree(0, $tpl);

        return $treeStr;
    }


}