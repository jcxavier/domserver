/* $Id: test-hello.c 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should give CORRECT on the default problem 'hello'.
 *
 * @EXPECTED_RESULTS@: CORRECT
 */

#include <stdio.h>

int main()
{
	char hello[20] = "Hello world!";
	printf("%s\n",hello);
	return 0;
}
