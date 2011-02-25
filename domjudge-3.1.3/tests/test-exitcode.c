/* $Id: test-exitcode.c 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should fail with RUN-ERROR (due to exitcode != 0)
 *
 * @EXPECTED_RESULTS@: RUN-ERROR
 */

int main()
{
	return 1;
}
