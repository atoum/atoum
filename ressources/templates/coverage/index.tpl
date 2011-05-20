<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Language" content="en" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><tpl:projectName /></title>
		<link rel="stylesheet" media="screen" type="text/css" href="<tpl:rootUrl />screen.css" title="Screen" />
	</head>
	<body>
		<div id="page">
			<h1><tpl:projectName /></h1>
			<div id="content">
				<table cellpadding="0" cellspacing="0">
					<tpl:coverage id="coverage">
					<tr>
						<td class="bar">
							<div class="bar">
								<div class="background"></div>
								<div class="graph" style="width: <tpl:classCoverageValue />%"></div>
								<div class="label"><a href="<tpl:rootUrl /><tpl:classUrl />"><tpl:className /></a> <span><tpl:classCoverageValue />%</span></div>
							</div>
						</td>
					</tr>
					</tpl:coverage>
				</table>
			</div>
		<div>
	</body>
</html>
