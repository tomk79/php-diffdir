<?php
/**
 * tomk79/diffdir
 * 
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */

namespace tomk79\diffdir;

/**
 * tomk79/diffdir htmlreport
 * 
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */
class htmlreport{
	private $fs;
	private $before, $after, $conf = array();

	/**
	 * constructor
	 */
	public function __construct( $fs, $before, $after, $conf = array() ){
		$this->fs = $fs;
		$this->before = $before;
		$this->after = $after;
		$this->conf = $conf;
	}

	/**
	 * save diff report index HTML
	 */
	public function save_diff_report_index_html( $html_list ){
		ob_start();?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>diffdir</title>
		<style type="text/css">
			html, body{
				margin:0;padding:0;
				background-color:#fff;
				color:#333;
			}
			#outline{
				width:auto;
				max-height:100%;
				overflow: hidden;
			}
			#diffpreview{
				float:right;
				width:73%;
				max-height:100%;
			}
			#diffpreview iframe{
				width:100%;
			}
			#difflist{
				float:left;
				overflow:auto;
				width:25%;
				max-height:100%;
			}
			#difflist li{
				margin-top:1px;
				margin-bottom:1px;
			}
			#difflist li.dir{
				background-color:#f5f5f5;
				font-weight:bold;
			}
			#difflist li a{
				color:#000;
				text-decoration:none;
			}
			#difflist li.changed a{
				background-color:#dfd;
			}
			#difflist li.changed a:before{
				content:"[C]";
			}
			#difflist li.added a{
				background-color:#dfd;
			}
			#difflist li.added a:before{
				content:"[A]";
				color:#f90;
			}
			#difflist li.deleted a{
				color:#f00;
				background-color:#fdd;
			}
			#difflist li.deleted a:before{
				content:"[D]";
			}
		</style>
		<script>
			(function(){
				function refresh(){
					var outline = document.getElementById('outline');
					var diffpreview = document.getElementById('diffpreview');
					var iframe = document.getElementById('iframe');
					var difflist = document.getElementById('difflist');

					outline.style.height = window.innerHeight+'px';
					iframe.height = window.innerHeight;
				}
				window.onload = refresh;
				window.onresize = refresh;
			})();
		</script>
	</head>
	<body>
		<div id="outline">
			<div id="diffpreview">
				<iframe src="about:blank" name="diffpreview" id="iframe" border="0" frameborder="0"></iframe>
			</div>
			<div id="difflist">
				<ul><?= $html_list; ?></ul>
			</div>
		</div>
	</body>
</html>
<?php
		$html_list = ob_get_clean();
		$this->fs->save_file($this->conf['output'].'/report/index.html', $html_list);
	}

	/**
	 * save diff report HTML
	 */
	public function save_diff_report_html( $repo ){
		$diff = new \cogpowered\FineDiff\Diff;
		ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>diff: <?= htmlspecialchars($repo['path']); ?></title>
<style>
<?= file_get_contents( __DIR__.'/fess-1.1.2.css' ); ?>
</style>
<style>
	body{
		color:#333;
		margin:0;
		padding:0;
	}
	.theme_outline{
		margin:1em 1em;
	}
	ins{
		color:#000;
		background-color:#dfd;
		text-decoration:none;
	}
	del{
		color:#f00;
		background-color:#fdd;
		text-decoration:none;
	}
</style>
</head>
<body>
<div class="theme_outline">
<h1><?= htmlspecialchars($repo['path']); ?></h1>
<p>
<?php if( $repo['before_info']['type'] == $repo['after_info']['type'] ){ ?>
<?= htmlspecialchars($repo['after_info']['type']) ?>
<?php }elseif( !strlen($repo['before_info']['type']) ){ ?>
<?= htmlspecialchars($repo['after_info']['type']) ?>
<?php }elseif( !strlen($repo['after_info']['type']) ){ ?>
<?= htmlspecialchars($repo['before_info']['type']) ?>
<?php }else{ ?>
<?= htmlspecialchars($repo['before_info']['type']) ?> to <?= htmlspecialchars($repo['after_info']['type']) ?>
<?php } ?>
</p>
<div class="contents">

<div class="unit">
<?php if( $repo['before_info']['type'] == 'file' || $repo['after_info']['type'] == 'file' ){ ?>
<?php
	$bin_before = @$this->fs->read_file( $this->before.$repo['path'] );
	$bin_after  = @$this->fs->read_file( $this->after.$repo['path'] );
?>
	<div class="code"><pre><code><?= $diff->render(
	mb_convert_encoding( $bin_before, 'UTF-8', 'UTF-8,'.mb_detect_order()),
	mb_convert_encoding( $bin_after , 'UTF-8', 'UTF-8,'.mb_detect_order())
) ?></code></pre></div>
<?php }else{ ?>
	<p>This item is a directory.</p>
<?php } ?>
</div>

<div>
<table class="def" style="width:100%;">
	<thead>
		<tr>
			<th style="width:20%;">&nbsp;</th>
			<th style="width:40%;">before</th>
			<th style="width:40%;">after</th>
		</tr>
	</thead>
	<tr>
		<th>path</th>
		<td colspan="2"><?= htmlspecialchars($repo['path']) ?></td>
	</tr>
	<tr>
		<th>status</th>
		<td colspan="2"><?= htmlspecialchars($repo['status']) ?></td>
	</tr>
<?php foreach( $repo['before_info'] as $key=>$val ){ ?>
	<tr>
		<th><?= htmlspecialchars($key) ?></th>
		<?php if($key=='timestamp'){ ?>
			<td><?= htmlspecialchars((strlen($repo['before_info'][$key])?@date('Y-m-d H:i:s',$repo['before_info'][$key]):'---')) ?></td>
			<td><?= htmlspecialchars((strlen($repo['after_info'][$key])?@date('Y-m-d H:i:s',$repo['after_info'][$key]):'---')) ?></td>
		<?php }else{ ?>
			<td><?= htmlspecialchars((strlen($repo['before_info'][$key])?$repo['before_info'][$key]:'---')) ?></td>
			<td><?= htmlspecialchars((strlen($repo['after_info'][$key])?$repo['after_info'][$key]:'---')) ?></td>
		<?php } ?>
	</tr>
<?php } ?>
</table>
</div>
</div>
</div>
</body>
</html>
<?php
		$src_html_diff = ob_get_clean();
		$path_diffHtml = $this->conf['output'].'/report/diff/'.$repo['path'].'.diff.html';
		$this->fs->mkdir_r( dirname( $path_diffHtml ) );
		$this->fs->save_file( $path_diffHtml, $src_html_diff );
		return true;
	}

}