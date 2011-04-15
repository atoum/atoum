"=============================================================================
" Author:					Frédéric Hardy - http://blog.mageekbox.net
" Licence:					BSD
"=============================================================================
if !exists('b:current_syntax')
	syn case match

	syntax match atoumValue ':\s\+\zs\d\+\(\.\d\+\)[^.]*'
	highlight default AtoumValue guifg=Yellow ctermfg=Yellow

	syntax match atoumPrompt '^=*> '
	highlight default AtoumPrompt guifg=Cyan ctermfg=Cyan

	syntax match atoumClass '^> Run .\+'
	highlight default AtoumClass guifg=Cyan ctermfg=Cyan

	syntax match atoumFailure '^> Failure ([^)]\+) !'
	highlight default AtoumFailure term=bold cterm=bold guifg=White guibg=DarkRed ctermfg=White ctermbg=DarkRed

	syntax match atoumSuccess '^> Success ([^)]\+) !'
	highlight default AtoumSuccess term=bold cterm=bold guifg=White guibg=DarkGreen ctermfg=White ctermbg=DarkGreen

	syntax match atoumError '^==> Error \d\+ .*$'
	highlight default AtoumError term=bold cterm=bold guifg=DarkRed ctermfg=DarkRed

	syntax match atoumException '^==> Exception .*$'
	highlight default AtoumException term=bold cterm=bold guifg=Magenta ctermfg=Magenta

	syntax match atoumModeline '^/\*.\+$'
	highlight default AtoumModeline guifg=bg ctermfg=bg

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
