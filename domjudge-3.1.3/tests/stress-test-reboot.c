/* $Id: stress-test-reboot.c 3354 2010-08-19 19:05:43Z eldering $
 *
 * This will try to reboot the computer by calling 'reboot'. Of course
 * this should not work as this program should not run as root. This should
 * only generate a message to stderr, so result in NO-OUTPUT.
 *
 * @EXPECTED_RESULTS@: NO-OUTPUT
 */

#include <stdlib.h>

int main()
{
	return system("reboot");
}
