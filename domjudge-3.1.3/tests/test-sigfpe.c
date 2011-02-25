/* $Id: test-sigfpe.c 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should fail with RUN-ERROR due to integer division by zero,
 * giving an exitcode 136.
 *
 * @EXPECTED_RESULTS@: RUN-ERROR
 */

#include <stdio.h>

int main()
{
	int a = 0;
	int b;

	b = 10 / a;

	printf("%d\n",b);

	return 0;
}
