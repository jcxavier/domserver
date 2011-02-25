/* $Id: test-sleep.c 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should fail with a TIMELIMIT.
 *
 * @EXPECTED_RESULTS@: TIMELIMIT
 */

#include <unistd.h>

int main()
{
	while ( 1 ) sleep(1);

	return 0;
}
