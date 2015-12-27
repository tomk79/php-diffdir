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
	 * save diff report index HTML (header)
	 */
	public function save_diff_report_index_html_header(){
		$this->fs->copy_r(__DIR__.'/resources/', $this->conf['output'].'/report/resources/');
		ob_start();?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<title>diffdir</title>
		<link rel="stylesheet" href="./resources/fess-1.2.2.css" />
		<script src="./resources/jquery-1.10.1.min.js"></script>
		<link rel="stylesheet" href="./resources/bootstrap/css/bootstrap.css" />
		<link rel="stylesheet" href="./resources/bootstrap/css/bootstrap-theme.css" />
		<script src="./resources/bootstrap/js/bootstrap.js"></script>
		<link rel="stylesheet" href="./resources/common.css" />
		<script src="./resources/common.js"></script>
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
				<div class="btn-group" role="group">
					<button type="button" class="btn btn-default" onclick="showAllList();">すべて表示</button>
					<button type="button" class="btn btn-default" onclick="filterList('>li:not(.changed,.added,.deleted)');">差分のみ</button>
					<button type="button" class="btn btn-default" onclick="filterList('>li:not(.file)');">ファイルのみ</button>
				</div>
				<ul><?php
		$html = ob_get_clean();
		$this->fs->save_file($this->conf['output'].'/report/index.html', $html);
		return true;
	}

	/**
	 * save diff report index HTML
	 */
	public function save_diff_report_index_html_list( $html ){
		@error_log($html, 3, $this->conf['output'].'/report/index.html');
		return true;
	}

	/**
	 * save diff report index HTML (Footer)
	 */
	public function save_diff_report_index_html_footer(){
		ob_start();?>
</ul>
			</div>
		</div>
	</body>
</html>
<?php
		$html = ob_get_clean();
		@error_log($html, 3, $this->conf['output'].'/report/index.html');
		return true;
	}

	/**
	 * save diff report HTML
	 */
	public function save_diff_report_html( $repo ){
		$path_diffHtml = $this->conf['output'].'/report/diff/'.$repo['path'].'.diff.html';
		$path_base = $this->fs->get_relatedpath($this->conf['output'].'/report/', dirname($path_diffHtml));
		$diff = new \cogpowered\FineDiff\Diff;
		ob_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>diff: <?= htmlspecialchars($repo['path']); ?></title>
<link rel="stylesheet" href="<?= htmlspecialchars($path_base); ?>resources/fess-1.2.2.css" />
<script src="<?= htmlspecialchars($path_base); ?>resources/jquery-1.10.1.min.js"></script>
<link rel="stylesheet" href="<?= htmlspecialchars($path_base); ?>resources/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" href="<?= htmlspecialchars($path_base); ?>resources/bootstrap/css/bootstrap-theme.css" />
<script src="<?= htmlspecialchars($path_base); ?>resources/bootstrap/js/bootstrap.js"></script>
<link rel="stylesheet" href="<?= htmlspecialchars($path_base); ?>resources/common.css" />
<script src="<?= htmlspecialchars($path_base); ?>resources/common.js"></script>
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
	$ext = @$this->fs->get_extension( $repo['path'] );
	switch( strtolower( $ext ) ){
		// ウェブドキュメント類
		case 'html':
		case 'htm':
		case 'xhtml':
		case 'xhtm':
		case 'shtml':
		case 'shtm':
		case 'js':
		case 'css':
		case 'rss':
		case 'rdf':
		case 'inc':
		// テキスト類
		case 'text':
		case 'txt':
		case 'md':
		// プログラム言語類
		case 'php':
		case 'cgi':
		case 'pl':
		case 'rb':
		case 'py':
		case 'c':
		case 'cpp':
		case 'cs':
		case 'd':
		case 'go':
		case 'h':
		case 'hx':
		case 'java':
		case 'lisp':
		case 'lua':
		case 'sql':
		case 'scala':
		case 'sh':
		case 'bat':
		case 'vbs':
		case 'hs':
		case 'lhs':
		case 'as':
		// データファイル類
		case 'csv':
		case 'json':
		case 'ini':
		case 'conf':
		case 'yml':
		case 'mm':
		case 'xml':
		case 'svg':
		// 糖衣言語類
		case 'scss':
		case 'coffee':
		case 'styl':
		case 'jade':
			?>
	<div class="code"><pre><code><?= $diff->render(
	@mb_convert_encoding( $bin_before, 'UTF-8', 'SJIS-win,Shift-JIS,eucJP-win,EUC-JP,UTF-8,'.mb_detect_order()),
	@mb_convert_encoding( $bin_after , 'UTF-8', 'SJIS-win,Shift-JIS,eucJP-win,EUC-JP,UTF-8,'.mb_detect_order())
) ?></code></pre></div><?php
			break;
		case 'jpg':
		case 'jpeg':
		case 'jpe':
		case 'gif':
		case 'png':
			print '<div class="image-preview">';
			print '<div class="btn-group" role="group">';
			print '	<button type="button" class="btn btn-default" onclick="compareImagesInTwoColumns();">並べて比較</button>';
			print '	<button type="button" class="btn btn-default" onclick="compareImagesInPilingUp();">重ねて比較</button>';
			print '</div>';
			print '<div class="image-preview--twocolumns">';
			if(@strlen($bin_before)){
				print '<div class="image-preview--before">';
				print '<p>変更前</p>';
				print '<img src="data:image/png;base64,'.htmlspecialchars( base64_encode($bin_before) ).'" alt="変更前の画像プレビュー" />';
				print '</div>';
			}
			if(@strlen($bin_after)){
				print '<div class="image-preview--after">';
				print '<p>変更後</p>';
				print '<img src="data:image/png;base64,'.htmlspecialchars( base64_encode($bin_after) ).'" alt="変更後の画像プレビュー" />';
				print '</div>';
			}
			print '</div>';
			print '<div class="image-preview--pilingup">';
			print '</div>';
			print '</div>';
			break;
		default:
			print '<p>比較できない拡張子です。</p>';
			break;
	}
?>
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
		$this->fs->mkdir_r( dirname( $path_diffHtml ) );
		$this->fs->save_file( $path_diffHtml, $src_html_diff );
		return true;
	}

}
