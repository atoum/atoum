"=============================================================================
" Author:					Frédéric Hardy - http://blog.mageekbox.net
" Licence:					BSD
"=============================================================================
if !exists('b:current_syntax')
	syn case match

	syntax match atoumFirstLevelTitle '^> .*\.\.\.$' contains=atoumFirstLevelPrompt
	syntax match atoumFirstLevelTitle '^> Atoum .*$' contains=atoumFirstLevelPrompt
	syntax match atoumFirstLevelTitle '^> .\+\(: \)\@=:' contains=atoumFirstLevelPrompt
	highlight default atoumFirstLevelTitle guifg=Cyan ctermfg=Cyan

	syntax match atoumSecondLevelTitle '^=> .\+$' contains=atoumSecondLevelPrompt
	highlight default atoumSecondLevelTitle guifg=White ctermfg=White

	syntax match atoumFirstLevelPrompt '^> ' contained
	highlight default atoumFirstLevelPrompt guifg=White ctermfg=White

	syntax match atoumSecondLevelPrompt '^=> ' contained
	highlight default atoumSecondLevelPrompt guifg=Cyan ctermfg=Cyan

	syntax match atoumValue '\s\+\zs\d\+\(\.\d\+\)[^.]*.'
	highlight default atoumValue guifg=White ctermfg=White

	syntax region atoumFailureDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ failures\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumFailureTitle,atoumFailurePrompt,atoumFailureFile,atoumFailureDescription,diffRemoved,diffAdded,diffSubname,diffLine

	syntax match atoumFailureFile '.\+\(::\)\@!:$' contained
	highlight default atoumFailureFile guifg=Red ctermfg=Red

	syntax match atoumFailureTitle 'There \(is\|are\) \d\+ failures\?:$' contained
	highlight default atoumFailureTitle guifg=Red ctermfg=Red

	syntax match atoumFailureDescription '^.*$' contained
	highlight default atoumFailureDescription guifg=White ctermfg=White

	syntax match atoumFailurePrompt '^=> ' contained
	highlight default atoumFailurePrompt guifg=Red ctermfg=Red

	syntax region atoumErrorDetails matchgroup=atoumFirstLevelPrompt start='^> There \(is\|are\) \d\+ errors\?:$'rs=s+2 end="^\(> \|/\*\)"me=s-2 contains=atoumFirstLevelPrompt,atoumErrorTitle,atoumErrorMethodPrompt,atoumErrorMethod,atoumErrorDescriptionPrompt,atoumErrorDescription

	syntax match atoumErrorDescription '.*$' contained
	highlight default atoumErrorDescription guifg=White ctermfg=White

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
