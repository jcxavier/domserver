<?php

error_reporting(E_ALL);
ini_set('display_errors','On');


putenv ("NLS_LANG=AMERICAN_AMERICA.AL32UTF8");

function openConnection($username, $password)
{
    $conn_str = '
    (DESCRIPTION =
        (ADDRESS =
           (PROTOCOL = TCP)
           (HOST = oraalu.fe.up.pt)
           (PORT = 1521)
           (HASH = '.rand(0,99999999).')
         )
         (CONNECT_DATA = (SID = ALU))
    )';
    
    if ($c = oci_connect($username, $password, $conn_str))
        return $c;
        
    $err = oci_error();
    echo "Oracle Connect Error " . $err['message'];   
        
    return NULL;
}

function closeConnection($conn)
{
    oci_close($conn);
}

function execQuery($conn, $sql)
{
    $lastSemicolonIdx = strrpos($sql, ";");
    
    if ($lastSemicolonIdx !== false)
        $sql = substr($sql, 0, $lastSemicolonIdx);
    
    return oci_parse($conn, $sql);
}

function getColumnArray($stmt)
{
    oci_execute($stmt, OCI_DESCRIBE_ONLY);
    
    for ($i = 1; $i <= oci_num_fields($stmt); ++$i)
        $columns[$i-1] = oci_field_name($stmt, $i);
        
    return $columns;
}

function getRowMatrix($stmt)
{
    $idx = 0;
    $matrix[$idx++] = getColumnArray($stmt);
    
    oci_execute($stmt);
    
    while ($row = oci_fetch_array($stmt, OCI_ASSOC+OCI_RETURN_NULLS))
    {
        $i = 0;
        $newRow = array();
        
        foreach ($row as $item)
            $newRow[$i++] = $item;
        
        $matrix[$idx++] = $newRow;
    }

    return $matrix;
}

function compareMatrixes($correct, $submission)
{
    $errors = array();
        
    $arr = array_diff($correct[0], $submission[0]);
    $errors['columnNames'] = (sizeof($arr) > 0);
    
    $errors['columnTotal'] = (sizeof($correct[0]) != sizeof($submission[0]));
    $errors['rowTotal'] = (sizeof($correct) != sizeof($submission));
    $errors['rowDifferences'] = 0;
    
    for ($i = 1 ; $i < sizeof($correct) && $i < sizeof($submission); $i++)
    {    
        $arr = array_diff($correct[$i], $submission[$i]);
        $errors['rowDifferences'] += (sizeof($arr) > 0 ? 1 : 0);
    }
    
    if ($errors['rowDifferences'] == sizeof($correct))
        $errors['rowDifferences'] = sizeof($submission);
    
    var_dump($errors);
}

function printMatrixToHTML($matrix)
{
    echo "<table>\n";
    echo "<table border='1'>\n";
    
    foreach ($matrix as $row)
    {
        echo "<tr>\n";
        
        foreach ($row as $item)
            echo "<td>" . ($item !== null ? $item : "&nbsp;") . "</td>\n";
        
        echo "</tr>\n";
    }

    echo "</table>\n";
}



$sqlStudent='
    select sum(tamanho) total, designação design, nipc
    from entidade, documento
    where nipc=emissor
    group by nipc, designação;
';

$sqlTeacher='
    select nipc, designação, sum(tamanho) total
    from entidade, documento
    where nipc=emissor
    group by nipc, designação
';



$conn = openConnection("domjudge", "judgedoom");
   
$stmt = execQuery($conn, $sqlStudent);
$matrixStd = getRowMatrix($stmt);

$stmt = execQuery($conn, $sqlTeacher);
$matrixTch = getRowMatrix($stmt);
   
echo "</br>submission";   
printMatrixToHTML($matrixStd);

echo "</br>correct";
printMatrixToHTML($matrixTch);

echo "</br>errors: ";
compareMatrixes($matrixTch, $matrixStd);

?>

