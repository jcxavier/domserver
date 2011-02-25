/* $Id: test-hello-lf-cr.c 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should give CORRECT or WRONG-ANSWER on the default problem
 * 'hello' depending on whether lf-cr line ending is ok.
 * Can also return PRESENTATION-ERROR if enabled.
 *
 * @EXPECTED_RESULTS@: CORRECT,WRONG-ANSWER,PRESENTATION-ERROR
 */

#include <stdio.h>

int main()
{
	char hello[20] = "Hello world!";
	printf("%s\n\r",hello);
	return 0;
}
