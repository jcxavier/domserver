/* $Id: test-compile-warning.c 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should give compiler warnings and fail with NO-OUTPUT
 *
 * @EXPECTED_RESULTS@: NO-OUTPUT
 */

#include <stdio.h>

int main()
{
	char str[1000];

	gets(str);

	return 0;
}
