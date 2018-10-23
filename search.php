<?php include "include/ini.php";
if(isset($_GET['userid']) && isset($_GET['name']) && isset($_GET['unitid'])){

  
  $wherestr=array();
  //组装where
  if(!empty($_GET['userid'])){
      
      $wherestr[]="user.userid LIKE '%".$_GET['userid']."%'";    
  }

  if(!empty($_GET['name'])){
      
      $wherestr[]="user.name LIKE '%".$_GET['name']."%'";    
  }

  if($_GET['unitid'] != 0){
      
      $wherestr[]='user.unitid = '.$_GET['unitid'];    
  }

    $where='';

    if(count($wherestr) > 0){

      $where = " where ".implode('and ',$wherestr);
    }

   

    //分页
    $page = isset($_GET['page']) ? $_GET['page'] : 1;

    $limit = 5;  //每页显示多少条数据
    $size = 5;   //每页显示多少个链接数

      $start = ($page-1)*$limit; // 开始位置

$sql = "SELECT user.id,user.userid,user.name,user.password,user.role,user.deleteTag,unit.name AS u_name FROM user LEFT JOIN unit  ON user.unitid=unit.unitid".$where." ORDER BY user.id ASC LIMIT $start , $limit";

    $admin_list=get_all($sql);

    //关键字高亮
     for ($i=0; $i < count($admin_list) ; $i++) { 
       
          $admin_list[$i]['userid']=str_replace($_GET['userid'],"<font color='red'>".$_GET['userid']."</font>",$admin_list[$i]['userid']);
          $admin_list[$i]['name']=str_replace($_GET['name'],"<font color='red'>".$_GET['name']."</font>",$admin_list[$i]['name']);
     }



    //数据总长度
    $sql = "SELECT COUNT(*) AS c FROM user".$where;
    $admin_count = get_one($sql);  

    

    //调用分页
$page_str = page($page,$admin_count['c'],$limit,$size,$class='flickr');
    


      }else{


    show_msg('错误操作！','index.php');


    }



//单位数据
  $sql="SELECT * FROM unit";
  $unit_data=get_all($sql);





 ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <title>长沙市报废汽车拆解远程监控系统</title>
  <link rel="stylesheet" href="css/layui.css">
  <link rel="stylesheet" href="css/page.css" />
    <script src="js/jquery-1.11.2.min.js"></script>
    <style>
    .upd{
      display: inline-block;
height: 30px;
line-height: 30px;
padding: 0 10px;
font-size: 12px;
background-color:  #FFB800;
border-radius: 2px;
    }

     .del{
      display: inline-block;
height: 30px;
line-height: 30px;
padding: 0 10px;
font-size: 12px;
background-color:  #FF5722;
margin-left: 10px;
border-radius: 2px;
    }

    .add_res{
    margin-left: 20px;
    display: inline-block;
    height: 38px;
    line-height: 38px;
    padding: 0 18px;
    background-color: #009688;
    color: #fff;
    white-space: nowrap;
    text-align: center;
    font-size: 14px;
    border: none;
    border-radius: 2px;
    cursor: pointer;
    font-family: inherit;
    font-style: inherit;
    font-weight: inherit;
  }

  .add_res:hover {

    opacity: .8;
 
    color: #fff
  }
    </style>
</head>
<body>
 

<body class="layui-layout-body">
<?php include "include/top.php" ?>
  
<?php include "include/menu.php"; ?>
  
  

  <div class="layui-body">     
 

 <form class="layui-form" action="javascript:" method="post">

<blockquote class="layui-elem-quote">搜索管理员</blockquote>
<div  class="layui-form-item">
  <label class="layui-form-label">账号：</label>
  <div style="width: 180px;" class="layui-input-inline">
      <input type="text" value="<?php echo $_GET['userid'] ?>"  name="userid"   autocomplete="off" class="layui-input" >
    </div>
    
      <label class="layui-form-label">姓名：</label>
  <div style="width: 120px;" class="layui-input-inline">
      <input type="text" value="<?php echo $_GET['name'] ?>" name="name"  autocomplete="off" class="layui-input">
    </div>

       <label class="layui-form-label">单位：</label>
  <div style="width: 115px;" class="layui-input-inline">
      <select class="txt" name="unitid">
      <option value="0">请选择单位</option>
      <?php foreach ($unit_data as $key => $value) {?>
    <option <?php echo $value['unitid'] == $_GET['unitid'] ? 'selected="selected"':''; ?> value="<?php echo $value['unitid']; ?>"><?php echo $value['name']; ?></option> 
      <?php } ?>
     </select>
    </div>
    <button onclick="return sub();"  class="layui-btn">搜索</button>
    <button onclick="return rese_t();"  class="add_res">重置</button>
    <button onclick="window.location.href='admin_add.php'"  class="add_res">添加管理员</button>
 </div>
</form>
<blockquote class="layui-elem-quote">管理员列表</blockquote>

   <table class="layui-table">
     <colgroup>
    <col width="150">
    <col width="200">
    <col>
  </colgroup>
  <thead>
    <tr>
      <th width="18%">账号</th>
      <th width="14%"><p >名字</p></th>
      <th width="15%"><p >密码</p></th>
      <th width="9%"><p >单位</p></th>
      <th width="8%">角色</th>
      <th width="12%">状态</th>
      <th width="20%">操作</th>
      </tr> 
  </thead>
  <tbody>
  <?php foreach ($admin_list as $key => $value) {?>
    <tr>
      <td><?php echo $value['userid'] ?></td>
      <td><?php echo $value['name'] ?></td>
      <td><?php echo $value['password'] ?></td>
      <td><?php echo isset($value['u_name']) ? $value['u_name'] : "单位不存在"; ?></td>
      <td><?php switch ($value['role']) {
        case '0':
          echo '普通用户';
          break;
        case '1':
          echo '建立工单';
          break;
        case '2':
          echo '工单完结';
          break;
        case '3':
          echo '管理员';
          break;
        default:
          echo '普通用户';
          break;
      } ?></td>
        <td><?php echo $value['deleteTag'] ? '正常':'删除'; ?></td>
      <td><a class="upd" href="admin_edit.php?id=<?php echo $value['id']; ?>&page=<?php echo $page ?>">修改</a>
      <!-- <button class="layui-btn layui-btn-sm">暂停</button> <-->
      <a class="del" onclick="return confirm('确认删除？')" href="index.php?del_id=<?php echo $value['id'] ?>&page=<?php echo $page ?>">删除</a>
      </td>
    </tr>
  <?php } ?>
      </tbody>
</table>
      <?php echo $page_str; ?>
  </div>
  
  <div class="layui-footer">
    <!-- 底部固定区域 -->
    © 湖南新空间系统技术有限公司
  </div>
</div>
<script src="layui.js"></script>
<script>
//搜索提交
function sub(){
  var userid=$('input[name=userid]').val();
  var name=$('input[name=name]').val();
   var unitid=$('.layui-this').attr('lay-value');
   window.location.href="search.php?userid="+userid+"&name="+name+"&unitid="+unitid;
}

//搜索框清空
function rese_t(){
   window.location.href="search.php?userid=&name=&unitid=";
}

layui.use('form', function(){
  var form = layui.form; //只有执行了这一步，部分表单元素才会自动修饰成功
  
  //……
  
  //但是，如果你的HTML是动态生成的，自动渲染就会失效
  //因此你需要在相应的地方，执行下述方法来手动渲染，跟这类似的还有 element.init();
  form.render();
});      
</script>
</body>
</html>
      