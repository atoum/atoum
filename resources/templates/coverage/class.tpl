<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Language" content="en" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><tpl:projectName /> : code coverage of <tpl:className /></title>
		<link rel="stylesheet" media="screen" type="text/css" href="<tpl:relativeRootUrl />screen.css" title="Screen" />
	</head>
	<body>
		<div id="page">
			<h1><a href="<tpl:relativeRootUrl />"><tpl:projectName /></a> :  code coverage of <tpl:className /></h1>
			<div id="content">
				<ul class="classSummary">
					<li class="class">
						<tpl:classCoverageUnavailable>
							<div class="label">Class code coverage <span>n/a</span></div>
						</tpl:classCoverageUnavailable>
						<tpl:classCoverageAvailable>
							<div class="bar">
								<div class="background"></div>
								<div class="graph" style="width: <tpl:classCoverageValue />%"></div>
								<div class="label">Class code coverage <span><tpl:classCoverageValue />%</span></div>
							</div>
						</tpl:classCoverageAvailable>
					</li>
					<tpl:methods>
						<li class="methods">
							<ul>
								<tpl:method>
									<li>
										<tpl:methodCoverageUnavailable>
											<div class="label"><a href="#<tpl:methodName />"><tpl:methodName /></a> <span>n/a</span></div>
										</tpl:methodCoverageUnavailable>
										<tpl:methodCoverageAvailable>
											<div class="bar">
												<div class="background"></div>
												<div class="graph" style="width: <tpl:methodCoverageValue />%"></div>
												<div class="label"><a href="#<tpl:methodName />"><tpl:methodName /></a> <span><tpl:methodCoverageValue />%</span></div>
											</div>
										</tpl:methodCoverageAvailable>
									</li>
								</tpl:method>
							</ul>
						</li>
					</tpl:methods>
				</ul>
				<table cellpadding="0" cellspacing="0" class="source">
					<tr><th class="number">Line</th><th>Code</th></tr>
					<tpl:sourceFile>
						<tpl:line><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td><pre><tpl:code /></pre></td></tr></tpl:line>
						<tpl:coveredLine><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td class="covered"><pre><tpl:code /></pre></td></tr></tpl:coveredLine>
						<tpl:notCoveredLine><tr><td class="number"><tpl:anchor><a name="<tpl:method />"></a></tpl:anchor><tpl:lineNumber /></td><td class="notCovered"><pre><tpl:code /></pre></td></tr></tpl:notCoveredLine>
					</tpl:sourceFile>
				</table>
			</div>
		</div>
	</body>
</html>
