/* $Id: test-output-nonewline.c 3302 2010-08-09 18:39:20Z eldering $
 *
 * Writes one line of output without a trailing newline. This should
 * give WRONG-ANSWER and the diff output should show the line.
 *
 * @EXPECTED_RESULTS@: WRONG-ANSWER
 */

#include <stdio.h>

int main()
{
	printf("This line has no trailing newline");

	return 0;
}
