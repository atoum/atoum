<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Language" content="en" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Code coverage of <tpl:projectName /></title>
		<link rel="stylesheet" media="screen" type="text/css" href="screen.css" title="Screen" />
	</head>
	<body>
		<div id="page">
			<h1>Code coverage of <tpl:projectName /></h1>
			<div id="content">
				<ul class="projectSummary">
					<li>
						<tpl:coverageUnavailable>
							<div class="bar">
								<div class="label">Global code coverage <span>n/a</span></div>
							</div>
						</tpl:coverageUnavailable>
						<tpl:coverageAvailable>
							<div class="bar">
								<div class="background"></div>
								<div class="graph" style="width: <tpl:coverageValue />%"></div>
								<div class="label">Global code coverage <span><tpl:coverageValue />%</span></div>
							</div>
						</tpl:coverageAvailable>
					</li>
					<li class="classes">
						<ol>
							<tpl:classCoverage>
								<li>
									<tpl:classCoverageUnavailable>
										<div class="bar">
											<div class="label"><a href="<tpl:classUrl />"><tpl:className /></a> <span>n/a</span></div>
										</div>
									</tpl:classCoverageUnavailable>
									<tpl:classCoverageAvailable>
									<div class="bar">
										<div class="background"></div>
										<div class="graph" style="width: <tpl:classCoverageValue />%"></div>
										<div class="label"><a href="<tpl:classUrl />"><tpl:className /></a> <span><tpl:classCoverageValue />%</span></div>
									</div>
									</tpl:classCoverageAvailable>
								</li>
							</tpl:classCoverage>
						</ol>
					</li>
				</ul>
			</div>
		</div>
	</body>
</html>
