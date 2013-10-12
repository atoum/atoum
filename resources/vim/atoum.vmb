" Vimball Archiver by Charles E. Campbell, Jr., Ph.D.
UseVimball
finish
autoload/atoum.vim	[[[1
153
"=============================================================================
" Author:					Frédéric Hardy - http://blog.mageekbox.net
" Date:						Fri Sep 25 14:29:10 CEST 2009
" Licence:					BSD
"=============================================================================
if !exists('g:atoum#php')
	let g:atoum#php = 'php'
endif
if !exists('g:atoum#debug')
	let g:atoum#debug = 0
endif
if !exists('g:atoum#_')
	let g:atoum#_ = ''
endif
"run {{{1
function atoum#run(file, bang, args)
	let _ = a:bang != '' ? g:atoum#_ : g:atoum#php . ' -f ' . a:file . ' -- -c ' . g:atoum#configuration

	if (_ != '')
		let g:atoum#_ = _
		let g:atoum#cursorline = &cursorline
		let bufnr = bufnr('%')
		let winnr = bufwinnr('^' . fnameescape(_) . '$')

		execute  winnr < 0 ? 'new ' . fnameescape(_) : winnr . 'wincmd w'

		set filetype=atoum
		setlocal buftype=nowrite bufhidden=wipe nobuflisted noswapfile nowrap nonumber nocursorline

		%d _

		let message = 'Execute ' . _ . ' ' . a:args . '…'

		call append(0, message)

		echo message

		2d _ | resize 1 | redraw

		execute 'silent! %!' . _ . ' ' . a:args . (g:atoum#debug ? ' --debug' : '')
		execute 'resize ' . line('$')
		execute 'nnoremap <silent> <buffer> <CR> :call atoum#run(''' . a:file . ''', '''', ''' . a:args . ''')<CR>'
		execute 'nnoremap <silent> <buffer> <LocalLeader>g :execute bufwinnr(' . bufnr . ') . ''wincmd w''<CR>'

		nnoremap <silent> <buffer> <C-W>_ :execute 'resize ' . line('$')<CR>
		nnoremap <silent> <buffer> <LocalLeader><CR> :call atoum#goToFailure(getline('.'))<CR>

		augroup atoum
		au!
		execute 'autocmd BufUnload <buffer> execute bufwinnr(' . bufnr . ') . ''wincmd w'''
		execute 'autocmd BufEnter <buffer> execute ''resize '' .  line(''$'')'
		autocmd BufEnter <buffer> let g:atoum#cursorline = &cursorline | set nocursorline | call atoum#highlightStatusLine()
		autocmd BufLeave <buffer> if (g:atoum#cursorline) | set cursorline | endif
		autocmd BufWinLeave <buffer> au! atoum
		augroup end

		let g:atoum#success = search('^Success ', 'w')

		if (g:atoum#success > 0)
			execute g:atoum#success
		else
			let result = getline(1, '$')

			let oldErrorFormat = &errorformat

			let &errorformat = 'In\ file\ %f\ on\ line\ %l\,\ %m'

			cgete filter(result, 'v:val =~ "^In file "')

			let &errorformat = oldErrorFormat

			let failure = search('^Failure ', 'w')

			if (failure > 0)
				execute failure
			endif
		endif

		call atoum#highlightStatusLine()

		echo ''
	endif
endfunction
"defineConfiguration {{{1
function atoum#defineConfiguration(directory, configuration, extension)
	augroup atoumConfiguration
	silent! execute 'au BufEnter *' . a:extension . ' if (expand(''%:p'') =~ ''^' . a:directory . ''') | let g:atoum#configuration = ''' . a:configuration . ''' | endif'
	augroup end
endfunction
"goToFailure {{{1
function atoum#goToFailure(line)
	let pattern = '^In file \(\f\+\) on line \(\d\+\).*$'

	if (matchstr(a:line, pattern) != '')
		execute bufwinnr('^' . substitute(a:line, pattern, '\1', '') . '$') . 'wincmd w'
		execute substitute(a:line, pattern, '\2', '')
		wincmd _
	endif
endfunction
"makeVimball {{{1
function atoum#makeVimball()
	split atoumVimball

	setlocal bufhidden=delete
	setlocal nobuflisted
	setlocal noswapfile

	let files = 0

	for file in split(globpath(&runtimepath, '**/atoum*'), "\n")
		for runtimepath in split(&runtimepath, ',')
			if file =~ '^' . runtimepath
				if getftype(file) != 'dir'
					let files += 1
					call setline(files, substitute(file, '^' . runtimepath . '/', '', ''))
				else
					for subFile in split(glob(file . '/**'), "\n")
						if getftype(subFile) != 'dir'
							let files += 1
							call setline(files, substitute(subFile, '^' . runtimepath . '/', '', ''))
						endif
					endfor
				endif
			endif
		endfor
	endfor

	try
		execute '%MkVimball! atoum'

		setlocal nomodified
		bwipeout

		echomsg 'Vimball is in ''' . getcwd() . ''''
	catch /.*/
		call atoum#displayError(v:exception)
	endtry
endfunction
"highlightStatusLine {{{1
function atoum#highlightStatusLine()
	if g:atoum#success
		hi statusline guibg=DarkGreen guifg=White gui=NONE
	else
		hi statusline guibg=DarkRed guifg=White gui=NONE
	endif
endfunction
"displayError {{{1
function atoum#displayError(error)
	echohl ErrorMsg
	echomsg a:error
	echohl None
endfunction
" vim:filetype=vim foldmethod=marker shiftwidth=3 tabstop=3
doc/atoum.txt	[[[1
54
*atoum.txt*	Plugin for using atoum, the simple, modern and intuitive unit
testing framework for PHP 5.3+

                                                 *atoum* *atoum-plugin*
	Contents:

		Introduction.............|atoum-introduction|
		Variables...................|atoum-variables|
		Commands.....................|atoum-commands|
		Mappings.....................|atoum-mappings|

Author:  Frederic Hardy <frederic.hardy@mageekbox.net>   *atoum-author*
Licence: BSD

This plugin is only available if 'compatible' is not set.

INTRODUCTION                                    *atoum-introduction*

Install in ~/.vim, or in ~\vimfiles if you're on Windows and feeling lucky.

If you're in a hurry to get started, here are some things to try:

Open a PHP file which contain atoum unit test, run |:Atoum|. A buffer
will be open by VIM to display report about unit test execution.

VARIABLES                                        *atoum-variables*

g:atoum#disable         If its value is 1, disable the plug-in.

g:atoum#configuration   Path to atoum configuration file which will be used
                        by atoum to execute unit tests.

g:atoum#php             Path to php binary which will be used to execute
                        unit tests.

COMMANDS                                        *atoum-commands*

These commands are only available if current buffer contains a PHP file.

                                                *atoum-:Atoum*
:Atoum                  Run unit tests in current buffer.

MAPPINGS                                        *atoum-mappings*

These maps are available in buffer opened by atoum plug-ins.

                                                *atoum-<CR>*
<CR>                    Re-execute unit tests.

                                                *fugitive-<localleader><CR>*
<localleader><CR>       If cursor is on a failure, go to the unit test file
                        at line of failure.

 vim:ts=8 sw=8 noexpandtab tw=78 ft=help:
ftplugin/php/atoum.php	[[[1
25
<?php

/*
Sample atoum configuration file.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use
  \mageekguy\atoum
;

/*
Write all on stdout.
*/
$stdOutWriter = new atoum\writers\std\out();

/*
Generate a CLI report.
*/
$vimReport = new atoum\reports\asynchronous\vim();
$vimReport
  ->addWriter($stdOutWriter)
;

$runner->addReport($vimReport);
ftplugin/php/atoum.vim	[[[1
29
"=============================================================================
" Author:					Frédéric Hardy - http://blog.mageekbox.net
" Date:						Fri Sep 25 14:48:22 CEST 2009
" Licence:					BSD
"=============================================================================
if (!exists('atoum#disable') || atoum#disable <= 0) && !exists('b:atoum_loaded')
	let b:atoum_loaded = 1

	if &cp
		echomsg 'No compatible mode is required by atoum.vim'
	else
		let s:cpo = &cpo
		setlocal cpo&vim

		if !exists('g:atoum#configuration')
			let g:atoum#configuration = expand('<sfile>:h') . '/atoum.php'
		endif

		command -buffer -nargs=* -bang Atoum call atoum#run(expand('%'), '<bang>', '<args>')
		command -buffer -nargs=0 AtoumDebugSwitch let g:atoum#debug=!g:atoum#debug | echomsg 'Atoum debug mode ' . (g:atoum#debug ? 'enabled' : 'disabled')
		command -buffer -nargs=0 AtoumVimball call atoum#makeVimball()

		let &cpo = s:cpo
		unlet s:cpo
	endif
endif

finish
" vim:filetype=vim foldmethod=marker shiftwidth=3 tabstop=3
syntax/atoum.vim	[[[1
189
"=============================================================================
" Author:					Frédéric Hardy - http://blog.mageekbox.net
" Licence:					BSD
"=============================================================================
if !exists('b:current_syntax')
	syn case match

	syntax match atoumFirstLevelTitle '^> .*\.\.\.$' contains=atoumFirstLevelPrompt
	syntax match atoumFirstLevelTitle '^> .\+\(:\)\@=:' contains=atoumFirstLevelPrompt
	syntax match atoumFirstLevelTitle '^> atoum .*$' contains=atoumFirstLevelPrompt
	highlight default atoumFirstLevelTitle guifg=Cyan ctermfg=Cyan

	syntax match atoumSecondLevelTitle '^=> .\+$' contains=atoumSecondLevelPrompt
	highlight default atoumSecondLevelTitle guifg=White ctermfg=White

	syntax match atoumFirstLevelPrompt '^> ' contained
	highlight default atoumFirstLevelPrompt guifg=White ctermfg=White

	syntax match atoumSecondLevelPrompt '^=> ' contained
	highlight default atoumSecondLevelPrompt guifg=Cyan ctermfg=Cyan

	syntax match atoumValue '\s\+\zs\d\+\(\.\d\+\)[^.]*.'
	syntax match atoumValue ' .\+$'
	highlight default atoumValue guifg=White ctermfg=White

	syntax region atoumTestDetails matchgroup=atoumFirstLevelPrompt start='^> .\+\.\.\.$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumTestTitle,atoumTestPrompt,AtoumTestTitle,AtoumTestResult

	syntax match atoumTestResult '.\+$' contained
	highlight default atoumTestResult guifg=White ctermfg=White

	syntax match atoumTestTitle '.\+\.\.\.$' contained
	highlight default atoumTestTitle guifg=LightBlue ctermfg=LightBlue

	syntax match atoumTestPrompt '^=> ' contained
	highlight default atoumTestPrompt guifg=LightBlue ctermfg=LightBlue

	syntax match atoumTestTitle '.\+:' contained
	highlight default atoumTestTitle guifg=LightBlue ctermfg=LightBlue

	syntax region atoumFailureDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ failures\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumFailureTitle,atoumFailurePrompt,atoumFailureMethod,atoumFailureDescription,diffRemoved,diffAdded,diffSubname,diffLine

	syntax match atoumFailureMethod '.\+\(::\)\@!:$' contained
	highlight default atoumFailureMethod guifg=Red ctermfg=Red

	syntax match atoumFailureTitle 'There \(is\|are\) \d\+ failures\?:$' contained
	highlight default atoumFailureTitle guifg=Red ctermfg=Red

	syntax match atoumFailureDescription '^.*$' contained
	highlight default atoumFailureDescription guifg=White ctermfg=White

	syntax match atoumFailurePrompt '^=> ' contained
	highlight default atoumFailurePrompt guifg=Red ctermfg=Red

	syntax region atoumErrorDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ errors\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumErrorTitle,atoumErrorMethodPrompt,atoumErrorMethod,atoumErrorDescriptionPrompt,atoumErrorDescription,atoumErrorValue

	syntax match atoumErrorValue '^.*$' contained
	highlight default atoumErrorValue guifg=White ctermfg=White

	syntax match atoumErrorDescription 'Error .\+:$' contained
	highlight default atoumErrorDescription guifg=Yellow ctermfg=Yellow

	syntax match atoumErrorMethod '.\+::.\+():$' contained
	highlight default atoumErrorMethod guifg=Yellow ctermfg=Yellow

	syntax match atoumErrorTitle 'There \(is\|are\) \d\+ errors\?:$' contained
	highlight default atoumErrorTitle guifg=Yellow ctermfg=Yellow

	syntax match atoumErrorMethodPrompt '^=> ' contained
	highlight default atoumErrorMethodPrompt guifg=Yellow ctermfg=Yellow

	syntax match atoumErrorDescriptionPrompt '^==> ' contained
	highlight default atoumErrorDescriptionPrompt guifg=Yellow ctermfg=Yellow

	syntax region atoumUncompletedMethodDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ uncompleted methods\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumUncompletedMethodTitle,atoumUncompletedMethodMethodPrompt,atoumUncompletedMethodMethod,atoumUncompletedMethodDescriptionPrompt,atoumUncompletedMethodDescription,atoumUncompletedMethodOutput

	syntax match atoumUncompletedMethodOutput '.*$' contained
	highlight default atoumUncompletedMethodOutput guifg=White ctermfg=White

	syntax match atoumUncompletedMethodDescription '.\+::.\+() with exit code [^:]\+:' contained
	highlight default atoumUncompletedMethodDescription guifg=Brown ctermfg=Brown

	syntax match atoumUncompletedMethodMethod '.\+::.\+():$' contained
	highlight default atoumUncompletedMethodMethod guifg=Brown ctermfg=Brown

	syntax match atoumUncompletedMethodTitle 'There \(is\|are\) \d\+ uncompleted methods\?:$' contained
	highlight default atoumUncompletedMethodTitle guifg=Brown ctermfg=Brown

	syntax match atoumUncompletedMethodMethodPrompt '^=> ' contained
	highlight default atoumUncompletedMethodMethodPrompt guifg=Brown ctermfg=Brown

	syntax match atoumUncompletedMethodDescriptionPrompt '^==> ' contained
	highlight default atoumUncompletedMethodDescriptionPrompt guifg=Brown ctermfg=Brown

	syntax region atoumVoidDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ void methods\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumVoidTitle,atoumVoidMethodPrompt,atoumVoidMethod,atoumVoidDescriptionPrompt

	syntax match atoumVoidMethod '.\+::.\+()$' contained
	highlight default atoumVoidMethod guifg=White ctermfg=White

	syntax match atoumVoidTitle 'There \(is\|are\) \d\+ void methods\?:$' contained
	highlight default atoumVoidTitle guifg=Blue ctermfg=Blue

	syntax match atoumVoidMethodPrompt '^=> ' contained
	highlight default atoumVoidMethodPrompt guifg=Blue ctermfg=Blue

	syntax region atoumSkippedDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ skipped methods\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumSkippedTitle,atoumSkippedMethodPrompt,atoumSkippedMethod,atoumSkippedDescriptionPrompt

	syntax match atoumSkippedMethod '.\+::.\+()$' contained
	highlight default atoumSkippedMethod guifg=DarkGrey ctermfg=White

	syntax match atoumSkippedTitle 'There \(is\|are\) \d\+ skipped methods\?:$' contained
	highlight default atoumSkippedTitle guifg=DarkGrey ctermfg=DarkGrey

	syntax match atoumSkippedMethodPrompt '^=> ' contained
	highlight default atoumSkippedMethodPrompt guifg=DarkGrey ctermfg=DarkGrey

	syntax region atoumExceptionDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ exceptions\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumExceptionTitle,atoumExceptionMethodPrompt,atoumExceptionMethod,atoumExceptionDescriptionPrompt,atoumExceptionDescription

	syntax match atoumExceptionDescription '.*$' contained
	highlight default atoumExceptionDescription guifg=White ctermfg=White

	syntax match atoumExceptionMethod '.\+::.\+():$' contained
	syntax match atoumExceptionMethod 'An exception has been thrown in .\+ on line \d\+:' contained
	highlight default atoumExceptionMethod guifg=Magenta ctermfg=Magenta

	syntax match atoumExceptionTitle 'There \(is\|are\) \d\+ exceptions\?:$' contained
	highlight default atoumExceptionTitle guifg=Magenta ctermfg=Magenta

	syntax match atoumExceptionMethodPrompt '^=> ' contained
	highlight default atoumExceptionMethodPrompt guifg=Magenta ctermfg=Magenta

	syntax match atoumExceptionDescriptionPrompt '^==> ' contained
	highlight default atoumExceptionDescriptionPrompt guifg=Magenta ctermfg=Magenta

	syntax region atoumCoverageDetails matchgroup=atoumFirstLevelPrompt start='^> Code coverage value:.\+$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumCoverageTitle,atoumCoverageClassPrompt,atoumCoverageMethodPrompt,atoumCoverageValue,atoumCoverageClass,atoumCoverageMethod

	syntax match atoumCoverageValue '.*$' contained
	highlight default atoumCoverageValue guifg=White ctermfg=White

	syntax match atoumCoverageTitle '.\+:' contained
	highlight default atoumCoverageTitle guifg=Green ctermfg=Green

	syntax match atoumCoverageClass 'Class .\+:' contained
	highlight default atoumCoverageClass guifg=Green ctermfg=Green

	syntax match atoumCoverageMethod '.\+::.\+():' contained
	highlight default atoumCoverageMethod guifg=Green ctermfg=Green

	syntax match atoumCoverageClassPrompt '^=> ' contained
	highlight default atoumCoverageClassPrompt guifg=Green ctermfg=Green

	syntax match atoumCoverageMethodPrompt '^==> ' contained
	highlight default atoumCoverageMethodPrompt guifg=Green ctermfg=Green

	syntax region atoumOutputDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ outputs\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumOutputTitle,atoumOutputPrompt,atoumOutputMethod,atoumOutputDescription,diffRemoved,diffAdded,diffSubname,diffLine

	syntax match atoumOutputMethod '.\+\(::\)\@!:$' contained
	highlight default atoumOutputMethod guifg=Gray ctermfg=Gray

	syntax match atoumOutputTitle 'There \(is\|are\) \d\+ outputs\?:$' contained
	highlight default atoumOutputTitle guifg=Gray ctermfg=Gray

	syntax match atoumOutputDescription '^.*$' contained
	highlight default atoumOutputDescription guifg=White ctermfg=White

	syntax match atoumOutputPrompt '^=> ' contained
	highlight default atoumOutputPrompt guifg=Gray ctermfg=Gray

	syntax match atoumSuccess '^Success ([^)]\+)!'
	highlight default atoumSuccess term=bold cterm=bold guifg=White guibg=DarkGreen ctermfg=White ctermbg=DarkGreen

	syntax match atoumFailure '^Failure ([^)]\+)!'
	highlight default atoumFailure term=bold cterm=bold guifg=White guibg=DarkRed ctermfg=White ctermbg=DarkRed

	syntax match diffRemoved	"^-.*"
	syntax match diffAdded	"^+.*"

	syntax match diffSubname	" @@..*"ms=s+3 contained
	syntax match diffLine	"^@.*" contains=diffSubname
	syntax match diffLine	"^\<\d\+\>.*"
	syntax match diffLine	"^\*\*\*\*.*"

	highlight default link diffRemoved Special
	highlight default link diffAdded Identifier
	highlight default link diffSubname PreProc
	highlight default link diffLine Statement

	let b:current_syntax = "atoum"
endif
" vim:filetype=vim foldmethod=marker shiftwidth=3 tabstop=3
