<?php
namespace plugins\message\model;//Demo插件英文名，改成你的插件英文就行了
use think\Model;

//Demo插件英文名，改成你的插件英文就行了,插件数据表最好加个plugin前缀再加表名,这个类就是对应“表前缀+plugin_demo”表
class MessageNoteModel extends Model
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = true;
}