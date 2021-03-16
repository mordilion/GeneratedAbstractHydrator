%skip whitespace \s+

%token parenthesis_ <
%token _parenthesis >
%token bracket_ \(
%token _bracket \)
%token empty_string ""|''
%token number       (\+|\-)?(0|[1-9]\d*)(\.\d+)?
%token null         null
%token comma        ,
%token name         (?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*

%token quote_                                "            -> quoted_string
%token quoted_string:quoted_string           [^"]+
%token quoted_string:_quote                  "            -> default
%token apostrophe_                           '            -> apostrophed_string
%token apostrophed_string:apostrophed_string [^']+
%token apostrophed_string:_apostrophe        '            -> default

type:
    simple_type() | object_type() | complex_type()
#simple_type:
    <name>
    | <number>
    | <null>
    | <empty_string>
    | ::quote_:: <quoted_string> ::_quote::
    | ::apostrophe_:: <apostrophed_string> ::_apostrophe::
#object_type:
    <name>
    ::bracket_::
    type()
    ( ::comma:: type() )*
    ::_bracket::
#complex_type:
    <name>
    ::parenthesis_::
    type()
    ( ::comma:: type() )*
    ::_parenthesis::