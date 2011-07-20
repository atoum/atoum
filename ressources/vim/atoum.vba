" Vimball Archiver by Charles E. Campbell, Jr., Ph.D.
UseVimball
finish
autoload/atoum.vim	[[[1
86
"=============================================================================
" Author:					Frédéric Hardy - http://blog.mageekbox.net
" Date:						Fri Sep 25 14:29:10 CEST 2009
" Licence:					GPL version 2.0 license
"=============================================================================
if !exists('g:atoum#php')
	let g:atoum#php = 'php'
endif
if !exists('g:atoum#_')
	let g:atoum#_ = ''
endif
"run {{{1
function atoum#run(file, bang)
	let _ = a:bang != '' ? g:atoum#_ : g:atoum#php . ' -f ' . a:file . ' -- -c ' . g:atoum#configuration

	if (_ != '')
		let g:atoum#_ = _
		let bufnr = bufnr('%')
		let winnr = bufwinnr('^' . _ . '$')
		silent! execute  winnr < 0 ? 'new ' . fnameescape(_) : winnr . 'wincmd w'
		silent! syntax on
		silent! set filetype=atoum
		setlocal buftype=nowrite bufhidden=wipe nobuflisted noswapfile nowrap number
		silent! :%d
		let message = 'Execute ' . _ . '...'
		call append(0, message)
		echo message
		silent! 2d | resize 1 | redraw
		silent! execute 'silent! %!'. _
		silent! execute 'resize ' . line('$')
		silent! execute 'syntax on'
		silent! execute 'autocmd BufUnload <buffer> execute bufwinnr(' . bufnr . ') . ''wincmd w'''
		silent! execute 'autocmd BufEnter <buffer> execute ''resize '' .  line(''$'')'
		silent! execute 'nnoremap <silent> <buffer> <CR> :call atoum#run(''' . a:file . ''', '''')<CR>'
		silent! execute 'nnoremap <silent> <buffer> <LocalLeader>g :execute bufwinnr(' . bufnr . ') . ''wincmd w''<CR>'
		nnoremap <silent> <buffer> <C-W>_ :execute 'resize ' . line('$')<CR>
	endif
endfunction
"defineConfiguration {{{1
function atoum#defineConfiguration(directory, configuration, extension)
	augroup atoum
	silent! execute 'au BufEnter *' . a:extension . ' if (expand(''%:p'') =~ ''^' . a:directory . ''') | let g:atoum#configuration = ''' . a:configuration . ''' | endif'
	augroup end
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
		echohl ErrorMsg
		echomsg v:exception
		echohl None
	endtry
endfunction
" vim:filetype=vim foldmethod=marker shiftwidth=3 tabstop=3
ftplugin/php/atoum.php	[[[1
28
<?php

/*
Sample atoum configuration file.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use
  \mageekguy\atoum,
  \mageekguy\atoum\cli\prompt
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

atoum\scripts\runner::getAutorunner()->getRunner()->addReport($vimReport);

?>
ftplugin/php/atoum.vim	[[[1
29
"=============================================================================
" Author:					Frédéric Hardy - http://blog.mageekbox.net
" Date:						Fri Sep 25 14:48:22 CEST 2009
" Licence:					GPL version 2.0 license
"=============================================================================
if (!exists('atoum#disable') || atoum#disable <= 0) && !exists('b:atoum_loaded')
	let b:atoum_loaded = 1

	if &cp
		echomsg 'No compatible mode is required by atoum'
	else
		let s:cpo = &cpo
		setlocal cpo&vim

		if !exists('g:atoum#configuration')
			let g:atoum#configuration = expand('<sfile>:h') . '/atoum.php'
		endif

		command -buffer -nargs=0 -bang Atoum call atoum#run(expand('%'), '<bang>')
		command -buffer -nargs=0 AtoumVimball call atoum#makeVimball()

		let &cpo = s:cpo
		unlet s:cpo
	endif
endif

finish

" vim:filetype=vim foldmethod=marker shiftwidth=3 tabstop=3
syntax/atoum.vim	[[[1
149
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

	syntax region atoumExceptionDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ exceptions\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumExceptionTitle,atoumExceptionMethodPrompt,atoumExceptionMethod,atoumExceptionDescriptionPrompt,atoumExceptionDescription

	syntax match atoumExceptionDescription '.*$' contained
	highlight default atoumExceptionDescription guifg=White ctermfg=White

	syntax match atoumExceptionMethod '.\+::.\+():$' contained
	syntax match atoumExceptionMethod 'Exception throwed in .\+ on line \d\+:' contained
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

	syntax match atoumSuccess '^Success ([^)]\+) !'
	highlight default atoumSuccess term=bold cterm=bold guifg=White guibg=DarkGreen ctermfg=White ctermbg=DarkGreen

	syntax match atoumFailure '^Failure ([^)]\+) !'
	highlight default atoumFailure term=bold cterm=bold guifg=White guibg=DarkRed ctermfg=White ctermbg=DarkRed

	syntax match atoumModeline '^/\*.\+$'
	highlight default atoumModeline guifg=bg ctermfg=bg

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
