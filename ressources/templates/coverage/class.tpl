<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Language" content="en" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><tpl:projectName /> : code coverage of <tpl:className /></title>
		<link rel="stylesheet" media="screen" type="text/css" href="<tpl:rootUrl />screen.css" title="Screen" />
	</head>
	<body>
		<div id="page">
			<h1>Code coverage of class <tpl:className /></h1>
			<div id="content">
				<ul class="classesSummary">
					<li class="class">
						<div class="bar">
							<div class="background"></div>
							<div class="graph" style="width: <tpl:classCoverageValue />%"></div>
							<div class="label"><a href="#<tpl:className />"><tpl:className /></a> <span><tpl:classCoverageValue />%</span></div>
						</div>
					</li>
					<li class="methods">
						<ul class="methods">
							<tpl:method id="method">
								<li>
									<div class="bar">
										<div class="background"></div>
										<div class="graph" style="width: <tpl:methodCoverageValue />%"></div>
										<div class="label"><a href="#<tpl:methodName />"><tpl:methodName /></a> <span><tpl:methodCoverageValue />%</span></div>
									</div>
								</li>
							</tpl:method>
						</ul>
					</li>
				</ul>
				<table cellpadding="0" cellspacing="0" class="source">
					<tr><th>Line</th><th>Code</th></tr>
					<tpl:source id="source">
						<tpl:codeLine id="blankLine"><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td><pre><tpl:code /></pre></td></tr></tpl:codeLine>
						<tpl:codeLine id="coveredLine"><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td class="covered"><pre><tpl:code /></pre></td></tr></tpl:codeLine>
						<tpl:codeLine id="notCoveredLine"><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td class="notCovered"><pre><tpl:code /></pre></td></tr></tpl:codeLine>
					</tpl:source>
				</table>
			</div>
		</div>
	</body>
</html>
