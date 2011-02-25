/* $Id: test-sigfpe.java 3302 2010-08-09 18:39:20Z eldering $
 *
 * This should fail with RUN-ERROR due to integer division by zero.
 *
 * @EXPECTED_RESULTS@: RUN-ERROR
 */

import java.io.*;

class Main {
    public static void main(String[] args) throws Exception {
		int a = 0;
		int b = 10 / a;
		System.out.println(b);
    }
}
