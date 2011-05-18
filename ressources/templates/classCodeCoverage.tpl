<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Language" content="en" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><tpl:projectName /> : code coverage of <tpl:className /></title>
		<link rel="stylesheet" media="screen" type="text/css" href="/screen.css" title="Screen" />
	</head>
	<body>
		<div id="page">
			<h1><tpl:className /></h1>
			<div id="content">
				<ul class="classesSummary">
					<tpl:class id="classSummary">
						<li class="class">
							<div class="bar">
								<div class="background"></div>
								<div class="graph" style="width: <tpl:averageCodeCoverage />%"></div>
								<div class="label"><a href="#<tpl:className />"><tpl:className /></a> <span><tpl:averageCodeCoverage />%</span></div>
							</div>
						</li>
					</tpl:class>
				</ul>
				<tpl:class id="class">
					<ul class="classSummary">
						<li class="class">
							<div class="bar">
								<div class="background"></div>
								<div class="graph" style="width: <tpl:averageCodeCoverage />%"></div>
								<div class="label"><a name="<tpl:className />"></a><tpl:className /> <span><tpl:averageCodeCoverage />%</span></div>
							</div>
						</li>
						<li class="methods">
							<ul class="methods">
								<tpl:methods id="methods">
									<li>
										<div class="bar">
											<div class="background"></div>
											<div class="graph" style="width: <tpl:codeCoverage />%"></div>
											<div class="label"><a href="#<tpl:anchor />"><tpl:method /></a> <span><tpl:codeCoverage />%</span></div>
										</div>
									</li>
								</tpl:methods>
							</ul>
						</li>
					</ul>
					<table cellpadding="0" cellspacing="0" class="source">
						<tr><th>Line</th><th>Call</th><th>Code</th></tr>
						<tpl:source id="source">
							<tpl:codeLine id="blankLine"><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td class="coverage"></td><td><pre><tpl:code /></pre></td></tr></tpl:codeLine>
							<tpl:codeLine id="coveredLine"><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td class="coverage"><tpl:coverage /></td><td class="covered"><pre><tpl:code /></pre></td></tr></tpl:codeLine>
							<tpl:codeLine id="notCoveredLine"><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td class="coverage">0</td><td class="notCovered"><pre><tpl:code /></pre></td></tr></tpl:codeLine>
						</tpl:source>
					</table>
				</tpl:class>
			</div>
		</div>
	</body>
</html>
