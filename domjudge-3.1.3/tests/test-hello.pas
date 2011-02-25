(* $Id: test-hello.pas 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should give CORRECT on the default problem 'hello'.
 *
 * @EXPECTED_RESULTS@: CORRECT
 *)

program helloworld(input, output);

var
   hello : string;

begin
   hello := 'Hello world!';
   writeln(hello);
end.
