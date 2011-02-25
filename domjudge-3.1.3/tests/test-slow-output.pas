(* $Id: test-slow-output.pas 3302 2010-08-09 18:39:20Z eldering $
 *
 * Output answers slowly, should give TIMELIMIT with some output
 *
 * @EXPECTED_RESULTS@: TIMELIMIT
 *)

program slowoutput(input, output);

var
   i,j,k,l,x : longint;

begin
   i := 0;
   while true do
   begin
	  inc(i,10);
	  x := 0;
	  for j := 1 to i do
	  begin
		 for k := 1 to i do
		 begin
			for l := 1 to i do
			begin
			   inc(x);
			end;
		 end;
	  end;
	  write(i);
	  write(' ^ 3 = ');
	  writeln(x);
   end;
end.
