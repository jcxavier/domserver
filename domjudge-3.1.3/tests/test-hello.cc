/* $Id: test-hello.cc 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should give CORRECT on the default problem 'hello'.
 *
 * @EXPECTED_RESULTS@: CORRECT
 */

using namespace std;

#include <iostream>
#include <string>

int main()
{
	string hello("Hello world!");
	cout << hello << endl;
	return 0;
}
