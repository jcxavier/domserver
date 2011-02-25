(* $Id: test-compile-warning.pas 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should give a compiler warning and fail with NO-OUTPUT
 *
 * @EXPECTED_RESULTS@: NO-OUTPUT
 *)

program warning(input, output);

var
   a : integer;

begin
   a := 1;
end.
