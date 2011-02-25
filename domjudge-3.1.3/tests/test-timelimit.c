/* $Id: test-timelimit.c 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should give a TIMELIMIT.
 *
 * @EXPECTED_RESULTS@: TIMELIMIT
 */

int main()
{
	int a;

	while ( 1 ) a++;

	return 0;
}
