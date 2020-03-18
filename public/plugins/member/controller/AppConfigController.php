<?php 
namespace plugins\member\controller; 
use cmf\controller\PluginAdminBaseController;//引入此类
use think\Db;

/**
 * 模块间访问配置控制器
 */
class AppConfigController extends PluginAdminBaseController
{
	public $uploadpath = '';


	protected function _initialize()
    {
        parent::_initialize();
        $adminId = cmf_get_current_admin_id();//获取后台管理员id，可判断是否登录
        if (!empty($adminId)) {
            $this->assign("admin_id", $adminId);
        }
        $this->uploadpath = ROOT_PATH.'public'.DS.'plugins'.DS.'member'.DS.'view'.DS.'public'.DS.'image'.DS.'icon';
    }
	/**
	 *配置首页(网页) 
	 */
	public function index()
	{
		//dump($this->uploadpath);die();
		$map['type']  = ['=',1];
		$search = [
			'stype' => '',
			'isshow' => 0,
			'keyword' => ''
		];
		if(request()->isPost()){
			$search = [
				'stype' => input('stype'),
				'isshow' => input('isshow'),
				'keyword' => input('keyword')
			];

			switch (input('stype')) {
				case '1':
					if (!empty(input('keyword'))) {
						$map['catname']  = ['like','%'.input('keyword').'%'];
					}
					break;
				
				default:
					
					break;
			}
			
			
			if (input('isshow') > 0 ) {
				if (input('isshow') == 1) {
					$map['status']  = ['=',1];
				}else{
					$map['status']  = ['=',0];
				}		
			}
		}
		$info = db('member_app_config')->where($map)->order('sort asc')->select();
		//dump($info);die();

		

		$this->assign(array(
            'info'=> $info,
            'search'=> $search
		));


		//检索所有对外配置的数据
		return $this->fetch();
	}



	/**
	 *配置首页(APP) 
	 */
	public function indexs()
	{
		
		$map['type']  = ['=',2];
		$search = [
			'stype' => '',
			'isshow' => 0,
			'keyword' => ''
		];
		if(request()->isPost()){
			$search = [
				'stype' => input('stype'),
				'isshow' => input('isshow'),
				'keyword' => input('keyword')
			];

			switch (input('stype')) {
				case '1':
					if (!empty(input('keyword'))) {
						$map['catname']  = ['like','%'.input('keyword').'%'];
					}
					break;
				
				default:
					
					break;
			}
			
			
			if (input('isshow') > 0 ) {
				if (input('isshow') == 1) {
					$map['status']  = ['=',1];
				}else{
					$map['status']  = ['=',0];
				}		
			}
		}
		$info = db('member_app_config')->where($map)->order('sort asc')->select();
		//dump($info);die();

		

		$this->assign(array(
            'info'=> $info,
            'search'=> $search
		));


		//检索所有对外配置的数据
		return $this->fetch();
	}


	/**
	 *添加栏目
	 */
	public function add()
    {
    	if(request()->isPost()){
   //  		$data = [
			// 	'code'=> 1,
			// 	'msg' => input('cimage'),
			// 	'data' => ''
			// ];
   //          return json_encode($data);die();
			$data = [
    			'catname'=>input('cname'),
    			'url'=>input('curl'),
    			'icon'=>input('cimage'),
    			'type'=>input('ctype'),
    			'status'=>input('cstaus')
    		];
    		
    		if(db('member_app_config')->insert($data)){

    			$datas= [
					'code'=> 1,
					'msg' => '添加栏目成功！',
					'data' => ''
				];
	            return $datas;
    			//return $this->success('添加栏目成功！');
    		}else{
    			$datas = [
					'code'=> 0,
					'msg' => '添加栏目失败！',
					'data' => ''
				];
	            return $datas;
    			//return $this->error('添加栏目失败！');
    		}
    		return;
    	}
        return $this->fetch();
    }


    /**
	 *添加栏目
	 */
	public function edit()
    {
        $id = input('id');
    	$info = db('member_app_config')->find($id);
   
    	if(request()->isPost()){
    		$data=[		
    			'catname'=>input('cname'),
    			'url'=>input('curl'),
    			'icon'=>input('cimage'),
    			'type'=>input('ctype'),
    			'status'=>input('cstaus')
    		];
    		
            $save = db('member_app_config')->where('id',input('cid'))->update($data);
    		if($save !== false){
    			$this->success('修改栏目信息成功！');
    		}else{
    			$this->error('修改栏目信息失败！');
    		}
    		return;
    	}
    	$this->assign('info',$info);
    	return $this->fetch();
    }

	
	

	/**
	 *排序
	 */
	public function listorder(){

		if(isset($_POST['listorders'])) {
            foreach($_POST['listorders'] as $id => $listorder) {
                $save=db('member_app_config')->where('id',$id)->setField('sort',$listorder);
            }
            return $this->success('操作成功！'/*,'index'*/);
        } else {
            return $this->error('操作失败！');
        }
	}


	/**
	 *显示or隐藏
	 */
	public function showandhidden()
    {
    	if ( empty(input('id')) || empty(input('type')) ) {
			$this->success('操作失败！');
    	}
    	if (input('type') == 1) {
    		$type = 1;
    	} elseif (input('type') == 2) {
    		$type = 0;
    	}
    	$save = db('member_app_config')->where('id',input('id'))->setField('status',$type);;
		if($save !== false){
			$this->success('操作成功！');
		}else{
			
		}
        
    }


    /**
	 *删除
	 */
	public function del()
    {
    	
		if(db('member_app_config')->delete(input('id'))){
			$this->success('删除成功！');
		}else{
			$this->error('删除失败！');
		}  	
        
    }




    public function uploadicon(){
	    // 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('file');
	    //exit(dump($file));
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    if($file){

	        $info = $file->move($this->uploadpath);
	        if($info){
	            // 成功上传后 获取上传信息
	            // 输出 jpg
	            //echo $info->getExtension();
	            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
	        	
				$data = [
					'code'=> 0,
					'msg' => '',
					'data' => [
						'src'=> str_replace("\\","/",$info->getSaveName())
					]
				];
	            
				return $data;
	            // 输出 42a79759f284b767dfcb2a0197904287.jpg
	            //echo $info->getFilename(); 
	        }else{
	            // 上传失败获取错误信息
	            return $file->getError();
	        }
	    }
	}



	/**
	 *获取网页配置
	 */
	public function getweb()
    {
    	$map['type']  = ['=',1];
		$map['status']  = ['=',1];
		$info = db('member_app_config')->where($map)->order('sort asc')->select();
		$data = [
			'status' => 'success',
			'message' => 'OK',
			'code' => 200,
			'data'=> $info 
		];
        echo json_encode($data,JSON_UNESCAPED_SLASHES);
        exit(0);
    }




    /**
	 *获取APP配置
	 */
	public function getapp()
    {
        $map['type']  = ['=',2];
		$map['status']  = ['=',1];
		$info = db('member_app_config')->where($map)->order('sort asc')->select();
		$data = [
			'status' => 'success',
			'message' => 'OK',
			'code' => 200,
			'data'=> $info 
		];
        exit(json_encode($data,JSON_UNESCAPED_SLASHES));
    }



}