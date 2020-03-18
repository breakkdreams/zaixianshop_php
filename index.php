<?php
header('Access-Control-Allow-Origin:*');
header('Access-Control-Allow-Methods:OPTIONS, GET, POST'); // 允许option，get，post请求
header('Access-Control-Allow-Headers:x-requested-with'); // 允许x-requested-with请求头


defined('IN_PHPCMS') or exit('No permission resources.');
//模型缓存路径
define('CACHE_MODEL_PATH',CACHE_PATH.'caches_model'.DIRECTORY_SEPARATOR.'caches_data'.DIRECTORY_SEPARATOR);
pc_base::load_app_func('util','content');
class index {
	private $db;
	function __construct() {
		$this->db = pc_base::load_model('content_model');
		$this->_userid = param::get_cookie('_userid');
		$this->_username = param::get_cookie('_username');
		$this->_groupid = param::get_cookie('_groupid');
	}
	//首页
	public function init() {
		if(isset($_GET['siteid'])) {
			$siteid = intval($_GET['siteid']);
		} else {
			$siteid = 1;
		}
		$siteid = $GLOBALS['siteid'] = max($siteid,1);
		define('SITEID', $siteid);
		$_userid = $this->_userid;
		$_username = $this->_username;
		$_groupid = $this->_groupid;
		//SEO
		$SEO = seo($siteid);
		$sitelist  = getcache('sitelist','commons');
		$default_style = $sitelist[$siteid]['default_style'];
		$CATEGORYS = getcache('category_content_'.$siteid,'commons');
		include template('content','index',$default_style);
	}
	
	
	
	//接取任务
		public function jiequfw() {
	
	
			
			
			include template('content','jiequfw');
		}
	
	//添加任务
		public function add_products() {
	
	
			
			
			include template('content','add-products');
		}
	//商品详情页
		public function shop_show() {
	
	
			
			
			include template('content','show');
		}
	
	//审核
			public function shenhe() {
	
	
			
			
			include template('content','shenhe');
		}
	
	//任务管理
	public function renwugl() {
	
	
			
			
			include template('content','renwugl');
		}
	//自由交易商品列表页
		public function shop_list() {
	
	
			
			
			include template('content','list');
		}
		//自由交易前台订单管理
		public function shop_order() {
	
	
			
			
			include template('content','shop_order');
		}
		//任务评价
		public function rw_pj() {
	
	
			
			
			include template('content','rw_pj');
		}
		//任务申诉
		public function rw_ss() {
	
	
			
			
			include template('content','rw_ss');
		}
		//任务详细
		public function rw_xx() {
	
	
			
			
			include template('content','rw_xx');
		}
	
		//副首页
		public function findex() {
	
	
			
			
			include template('content','findex');
		}
			//
		public function personal_center() {
	
	
			
			
			include template('content','personal_center');
		}
	
	public function shop_index() {
	
	
			
			
			include template('content','shop_index');
		}
	
	
	
}
?>