<?php
namespace app\pre\controller;
use think\Db;  //导入系统自带的Db类，该类专门用来访问数据库。
//导入模型类
use  app\common\model\Move;
use  app\common\model\News;
use  app\common\model\Upload;
use  think\Controller;
use  think\Session;

/**
 * @Author: 张三
 * @Date:   2021-09-14 08:59:41
 * @Last Modified by:   Marte
 * @email : test@165.com
 * @Last Modified time: 2021-10-26 16:24:44
 */

/**
*控制器类
*/
class NewsController extends Controller    //News是控制器类
{

    //个人介绍视图
    public function introduce()
    {
       $html = $this->fetch('introduce');
       return $html;
    }

    //个人爱好视图
     public function hobby()
    {
       $html = $this->fetch('hobby');
       return $html;
    }

   
    //个人经历视图
     public function person()
    {
       $html = $this->fetch('person');
       return $html;
    }
	//发布经历视图
	 public function fabu()
	{
	   $html = $this->fetch('fabu');
	   return $html;
	}
	
	//添加视图
	public function add($value='')
	{
	  // echo "add page........";
	 $sy=session('syg');
	 if($sy===null)
	 
	 return $this->error('请登录',url('news/administer'));
	 $htmls= $this->fetch();
	 return $htmls;
	}
	
	
    //后台人员登录视图
     public function administer()
    {
        $html=$this->fetch('administer');
        return $html;
    }

    //后台添加操作视图
    // public function tianjia()
    // {
    //     $html=$this->fetch('tianjia');
    //     return $html;
    // }
	
	//主页视图
	public function index1()
	{
	     $dy = new News;
	     $results =$dy->paginate(3);//select()
	     $this->assign('resultss',$results);
	     $html =$this->fetch("index1");
	     return $html;
	}
	//主页2
	public function index2()
	{
	  $sy=session("syg");
	  if ($sy===null){
	      return $this->error('你还未登录',url('news/index1'));
	  }
	  $dy = new News;
	  $results =$dy->paginate(3);//select()
	  $this->assign('resultss',$results);
	  $html =$this->fetch("index2");
	  return $html;
	}
	
	//图片上传
	public function upload()
	{
		$file = request()->file('file');
		$info = $file->validate(['size'=> 20000000,'ext'=>'jpg,png,gif,jpeg'])->move(ROOT_PATH .'public'. DS .'protent');
		if($info)
		{
			//路径
			$path=$info->getSaveName();
			
			$newsarr = Array();
			$newsarr['pic_file']=$path;
			$pic_fil = new Upload();
			$pic_fil->data($newsarr)->save();
			return $this->success("添加成功",url('news/center'));
		}else{
			echo "$file->getError";
		}	
	}
	
	//（center） 视图
	 public function center($value='')
	{
		$sy=session("syg");
		if ($sy===null){
		    return $this->error('你还未登录',url('news/index1'));
		}
		$dy = new Upload;
		$results =$dy->paginate(5);//select()
		$this->assign('picture',$results);
		$html =$this->fetch("center");
		return $html;
	}	
	

    //实现数据库与视图的交替
    public function tianjia($value='')
    {
        $jm=$_POST['jm'];
        // $zy=$_POST['zy'];   

        //建立数组
         $newsarr = Array();
         $newsarr['pai'] =$jm;
         // $newsarr['ziyuan'] = $zy;

        //把数组的值存储到数据库的move表
         $dy = new Paihang;
         $dy->data($newsarr)->save();
        $this->success('写入成功',url('news/houtai'));  
    }  

    //administer人员登录判断
   public function admin($value='')
    {
        $username=$_POST['name'];      
        $password=$_POST['password'];

        if ($username=="11" && $password=="11") {
            // 成功之后跳转
           // $this->success(提示信息,跳转地址,用户自定义数据,跳转跳转,header信息);
            // 跳转地址未设置时 默认返回上一个页面
			session("syg",$username);
            $this->success('登录成功',url('news/houtai'));  
           }else    
        {         
                // 失败之后跳转              
                 $this->error('登录失败',url('news/administer'));      
         } 
    }  

    // 处理登录的提交页面
    public function login()
    {
    //拿到视图页的name值
    $use=$_POST['username'];      
    $pas=$_POST['password'];
	session("syg",$use);
    //数据库表名 move
    $res = array('username' => $use);
	$row= move::get($res);
           if(!is_null($row))
              {
              if($row->getData('password')== $pas)
    
                return $this->success('登录成功，请观看',url('news/index2') );
              else{
				    return $this->error('密码不对，请重新输入');
			  }
            }
            else{
				   return $this->error('用户不存在，请注册');
			} 
    }
	
	 public function logout_out(){
	        $sy=session("syg");
	        if ($sy===null){
	            return $this->error('你还未登录',url('news/index1'));
	        }else{
	        Session::delete("syg");
	        $this->success('退出成功','index1');
	        }
	    }
		
	
	   public function register()
	    {
		   $regname=$_POST['registername'];
		   $regpas=$_POST['registerpassword'];
		   $regpas2=$_POST['registerpassword2'];
		    // 插入数据库
		   if ( $regpas!=$regpas2) {
		     $this->error('密码不一致,请重新输入',url('news/index1'));  
		   }else{
		    $ress = Array();
		    $ress['username']=$regname;
		    $ress['password']=$regpas;
		   //把数组的值存储到数据库的move表
		    $gist = new Move;
		    $gist->data($ress)->save();
		    $this->success('注册成功',url('news/index1'));  
		  
	     }
	   }
	   
	   
	  public function houtai($value='')
	  {
		  $sy=session('syg');
		  if($sy===null)
		  
			  return $this->error('请登录',url('news/administer'));  
		  
	           $xw=new News;
	  
	           $results=$xw->paginate(3);
	  
	           $this->assign('jg',$results);
	  
	           //取回视图页代码
	          $htmls= $this->fetch();
	           //把代码发给用户
	           return $htmls;
	  }
	  
	  public function save($value='')
	    {
			
		//找到变量
	    $bt=$_POST['title'];
	    $ly=$_POST['source'];
	    $lx=$_POST['type'];
	    $nr=$_POST['content'];
		$sp=$_POST['paihang'];
		$zy=$_POST['ziyuan'];
	
		//定义一个数组
	    $xw=new News;
	    $newsarr=array();
	    $newsarr['title']=$bt;
	    $newsarr['source']=$ly;
	    $newsarr['type']=$lx;
	    $newsarr['content']=$nr;
		$newsarr['paihang']=$sp;
		$newsarr['ziyuan']= $zy;
	  
	    // var_dump ($newsarr);
	  
	   $xw->data($newsarr)->save();  // 把数组存到news表里面
	  
	     return $this->success("数据发布成功",url('news/houtai'));
	  
	  }
	  
	  
	  public function edit($value='')
	  {
			//返回添加值的视图
	         $idx=$_GET["id"];
	         $thenews=News::get($idx);
	  
	          $this->assign('xw',$thenews);
	  
	  
	           //取回视图页代码
	          $htmls= $this->fetch();
	           //把代码发给用户
	           return $htmls;
	  }
	  
	  public function delete($value='')
	  {
	     $idx=$_GET["id"];
	     
	     //根据id找到序号对应的新闻
	     $thenews=News::get($idx);
	     //找到他，删除他
	     
	     $thenews->delete();
	     
	     return $this->success("删除成功",url('news/houtai'));
	  }
	  
	  
	  public function update($value='')
	  {
	      
	      $idx=$_POST['xh'];
	  
	     $thenews= News::get($idx);  //在News表里找到将更新的那一条新闻
	  
	      $bt=$_POST['title'];
	      $ly=$_POST['source'];
	      $lx=$_POST['type'];
	      $nr=$_POST['content'];
	      $zz=$_POST['author'];
		  $sp=$_POST['paihang'];
		  $zy=$_POST['ziyuan'];
	  
	      $newsarr=array();
	      $newsarr['title']=$bt;
	      $newsarr['source']=$ly;
	      $newsarr['type']=$lx;
	      $newsarr['content']=$nr;
	      $newsarr['author']=$zz;
		  $newsarr['paihang']=$sp;
		  $newsarr['ziyuan']= $zy;
	  
	      // var_dump ($newsarr);
	  
	     $thenews->data($newsarr)->save();  // 把数组存到news表里面
	  
	     return $this->success("数据更新成功",url('news/houtai'));
	  }
	  
	  
	  // 查询
	  public function query()
	  {
		  $keywords=$_POST['chaxun'];
		  $sle = new News();
		  //查询数据集
		 $results =  $sle->where('title|author|source|type|content|paihang|ziyuan','like','%'.$keywords.'%')
		       ->limit(5)
			   ->order('Id','desc')
			   ->paginate(3);
		$this->assign('jg',$results);  
		 //取回视图页代码
		$htmls= $this->fetch();
		 //把代码发给用户
		 return $htmls;
	  }
	  
	 
	 
	 
	 
	 
	 
	  
}
	 