(* $Id: test-sigfpe.pas 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should fail with RUN-ERROR due to a segmentation fault.
 *
 * @EXPECTED_RESULTS@: RUN-ERROR
 *)

program sigfpe(input, output);

var
   a,b : integer;

begin
   a := 1;
   b := 0;
   a := a div b;
end.
