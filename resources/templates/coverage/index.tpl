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
		<div id="header">
			<h1>Code coverage of <tpl:projectName /></h1>
			<h2>
				Global code coverage
				<span>
					<tpl:coverageUnavailable>n/a</tpl:coverageUnavailable>
					<tpl:coverageAvailable><tpl:coverageValue />%</tpl:coverageAvailable>
				</span>
			</h2>
		</div>

		<div id="page">
			<div id="content">
				<ul class="summary">
					<tpl:classCoverage>
						<li>
							<tpl:classCoverageUnavailable>
								<div class="bar">
									<div class="label"><tpl:className /> <span>n/a</span></div>
								</div>
							</tpl:classCoverageUnavailable>
							<tpl:classCoverageAvailable>
							<div class="bar">
								<div class="background"></div>
								<div class="graph" style="width: <tpl:classCoverageValue />%"></div>
								<div class="label">
									<span class="percent"><tpl:classCoverageValue />%</span>
									<a href="<tpl:classUrl />"><tpl:className /></a>
								</div>
							</div>
							</tpl:classCoverageAvailable>
						</li>
					</tpl:classCoverage>
				</ul>
			</div>
		</div>

		<div id="footer">
			<p>Code coverage report powered by <a href="http://atoum.org">atoum</a></p>
		</div>
	</body>
</html>
