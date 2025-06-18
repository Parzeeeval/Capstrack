<?php 

    try {
        $host = 'localhost';
        $dbname = 'u354989168_capstrack_db1';
        $dbusername = 'u354989168_admin01'; 
        $dbpassword = '@Capstrack2024';
    
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbusername, $dbpassword);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        $sql = "SELECT * FROM academic_year ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            $start_year = $result["start_year"];
            $end_year = $result["end_year"];
            $start_month = $result["start_month"];
            $end_month = $result["end_month"];
    
            $curr_year = date("Y");
            $curr_month = ltrim(date("m"), "0");
    
            if ($end_year == $curr_year) {
                if ($curr_month >= $end_month) {
                    $new_start_year = $curr_year + 1;
                    $new_end_year = $new_start_year + 1;
    
                    $sql = "INSERT INTO academic_year (start_year, start_month, end_year, end_month) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $result = $stmt->execute([$new_start_year, $start_month, $new_end_year, $end_month]);
                    
                    if ($result) {
                        echo '<script>';
                        echo 'console.log("Successfully created a new academic year with start year of: ' . $new_start_year . ', and end year of: ' . $new_end_year . '");';
                        echo '</script>';
    
                        $new_acadYearID = $conn->lastInsertId();
                        $prev_acadYearID = $new_acadYearID - 1;
    
                        // Start a transaction for section and student updates
                        $conn->beginTransaction();
                        
                        $sql = "SELECT * FROM sections WHERE academicYearID = ? AND yearLevel = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute([$prev_acadYearID, 3]);
                        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if (count($sections) >= 1) {
                            foreach ($sections as $section) {
                                $prev_sectionID = $section["sectionID"];
                                $coordinator = $section["coordinatorID"];
                                $courseID = $section["courseID"];
                                $sec_letter = $section["section_letter"];
                                $sec_group = $section["section_group"];
                                $specialization = $section["specialization"];
    
                                // Insert new section
                                $sql = "INSERT INTO sections (coordinatorID, courseID, yearLevel, section_letter, section_group, specialization, academicYearID, semester)
                                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                                $stmt = $conn->prepare($sql);
                                $verify1 = $stmt->execute([$coordinator, $courseID, 4, $sec_letter, $sec_group, $specialization, $new_acadYearID, 1]);
    
                                if ($verify1) {
                                    $new_sectionID = $conn->lastInsertId();
    
                                    // Fetch students in the previous section
                                    $sql = "SELECT * FROM students WHERE sectionID = ?";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute([$prev_sectionID]);
                                    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                                    foreach ($students as $student) {
                                        // Update each student with the new section ID
                                        $sql = "UPDATE students SET new_sectionID = ? WHERE sectionID = ?";
                                        $stmt = $conn->prepare($sql);
                                        $verify2 = $stmt->execute([$new_sectionID, $prev_sectionID]);
    
                                        if (!$verify2) {
                                            throw new Exception("Error updating student with new section ID");
                                        }
                                    }
                                } else {
                                    throw new Exception("Error inserting new section");
                                }
                            }
                        }
    
                        // Commit transaction after all updates
                        $conn->commit();
    
                    } 
                    
                    else {
                        throw new Exception("Error in creating a new academic year");
                    }
                } 
                
                else {
                    echo '<script>';
                    echo 'console.log("Current month not yet June");';
                    echo '</script>';
                }
            } 
            
            else {
                echo '<script>';
                echo 'console.log("Current year is still the same as starting year");';
                echo '</script>';
            }
        } 
        
        else {
            throw new Exception("Error getting academic year from database");
        }
    } 
    
    catch (Exception $e) {
        // Rollback transaction if an error occurs
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        echo '<script>';
        echo 'console.log("Error: ' . addslashes($e->getMessage()) . '");';
        echo '</script>';
    }

?>