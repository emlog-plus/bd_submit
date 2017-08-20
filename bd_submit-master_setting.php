<?php
!defined('EMLOG_ROOT') && exit('access deined!');
function  plugin_setting_view(){
	include(EMLOG_ROOT.'/content/plugins/bd_submit-master/bd_submit-master_config.php');
	?>
 <div class="heading-bg  card-views">
  <ul class="breadcrumbs">
  <li><a href="./"><i class="fa fa-home"></i> <?php echo langs('home')?></a></li>
  <li class="active">百度自动推送</li>
 </ul>
</div>
<?php if(isset($_GET['setting'])):?>
<div class="actived alert alert-success alert-dismissable">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
设置成功
</div>
<?php endif;?>
<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="panel-body"> 
<p>token与站点链接一一对应，如果错误插件无法正常工作   </p>
		<P>token请在百度平台：http://zhanzhang.baidu.com/linksubmit/index 手动获取 </P>
		<p>提交结果可以在 http://zhanzhang.baidu.com/sitemap/pingindex  查看 结果有一定延迟 </p>
		<p>提交错误请在<a href="http://blog.alw.pw/post-10.html">blog.alw.pw/post-10.html</a>查看错误原因，多参考他人评论或者留言</p>
		<p>清除提交记录将导致保存文章的时候会再次提交该文章！请慎重！慎重！慎重！重要的事情说三遍</p>
</div>
</div>
</div>
</div>    
<form action="./plugin.php?plugin=bd_submit-master&action=setting" method="POST">
<div class="row">
<div class="col-md-12">
<div class="panel panel-default card-view">
<div class="form-group">
<label class="control-label mb-10">
站点链接(不带http)</label>
<input class="form-control"  name="site" type="text" value="<?php echo $config['site'];?>" />
</div>
<div class="form-group">
<label class="control-label mb-10">
Token</label>
<input class="form-control"  type="text" name="token" value="<?php echo $config['token'];?>" />
</div>
<div class="form-group">
<label class="control-label mb-10">
显示记录前N条(0为全部显示)
</label>
<input class="form-control" type="text" name="log_number" value="<?php echo $config['log_number'];?>">
</div>
<div class="form-group">
<input type="checkbox" name="clean_log">
<label class="control-label mb-10">
清除提交记录(勾选后修改设置会清理所有记录)
</label>
</div>
<div class="clearfix"></div>
<div class="form-group text-center">
<input class="btn  btn-success" type="submit" value="修改设置">
</div>
	</div>
</div>
</div>  	
	</form>
	<?php 
	$submit_file = dirname(__FILE__).'/submit_log.txt';	
	$submit_logs = file_get_contents($submit_file);
	$submit_logs_info = explode("\r\n",$submit_logs);
	array_pop($submit_logs_info);
	?>
	<style type="text/css">
		td{
			padding-right: 5px;
		}
	</style>
	<table>
		<tbody>
		<tr><th>提交网址</th><th>提交状态</th><th>提交时间</th><th>错误原因</th></tr>
		<?php
		$i = ($config['log_number'] == 0) ? 0 : ($config['log_number'] + 1);

		foreach (array_reverse($submit_logs_info) as $submit_log) {
			$i--;
			if($i == 0) break;
			$submit_log_info = explode("||",$submit_log);
			if($submit_log_info[0] == 0)
				echo "<tr><td>".$submit_log_info[3]."</td><td>提交失败</td><td>".$submit_log_info[2]."</td><td>".$submit_log_info[1]."</td></tr>";
			else
				echo "<tr><td>".$submit_log_info[2]."</td><td>提交成功</td><td>".$submit_log_info[1]."</td><td>-</td></tr>";

		}
		?>
		</tbody>
	</table>	
	<?php
}

function plugin_setting(){
	if($_POST['clean_log'] == true){
		@file_put_contents(EMLOG_ROOT.'/content/plugins/bd_submit-master/submit_log.txt', "");
		@file_put_contents(EMLOG_ROOT.'/content/plugins/bd_submit-master/logid_log.txt', "");
	}
	$newconfig = '<?php
					$config =  array(
						"site" => "'.$_POST['site'].'",
						"token" => "'.$_POST['token'].'",
						"log_number" => "'.$_POST['log_number'].'" 
					);';
	@file_put_contents(EMLOG_ROOT.'/content/plugins/bd_submit-master/bd_submit-master_config.php', $newconfig);
}
?>