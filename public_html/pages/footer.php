
    </div>
    
        <?php
            require "connection.php";
 
        
            $courseID = 0;
            
            if(isset($_SESSION["courseID"])){
                $courseID = $_SESSION["courseID"];
            }
        
            $sql = "SELECT * FROM specializations WHERE courseID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$courseID]);
        
            $specializationCount = $stmt->rowCount(); 
        
            $redirectUrl = $specializationCount >= 1 ? "/specializations" : "/courses";
            
            //Block of code nato ay pang redirect back sa courses page kapag walang nadetect na specializations
        ?>
    
        <script>
        
            var menu = document.getElementById('menu');
            var hamburger = document.querySelector('.hamburger');
        
            hamburger.addEventListener('click', function() {
                menu.classList.toggle('expanded');
            });
            
            document.querySelectorAll('.menu-inner ul li a').forEach(item => {
                item.addEventListener('click', function() {
                    sessionStorage.setItem('activeMenu', this.getAttribute('href'));
            
                    document.querySelectorAll('.menu-inner ul li a').forEach(link => link.classList.remove('active'));
                    this.classList.add('active');
                });
            });
            
            // Highlight the active menu item on page load
            window.addEventListener('DOMContentLoaded', function() {
                const activeMenu = sessionStorage.getItem('activeMenu');
                if (activeMenu) {
                    const activeItem = document.querySelector(`.menu-inner ul li a[href="${activeMenu}"]`);
                    if (activeItem) {
                        activeItem.classList.add('active');
                    }
                }
            });
            
            function toggleNotifications() {
              const dropdown = document.getElementById('notificationDropdown');
              dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            }
            
            // Close the dropdown if clicking outside
            document.addEventListener('click', (event) => {
              const dropdown = document.getElementById('notificationDropdown');
              const bell = document.querySelector('.bell');
              if (!dropdown.contains(event.target) && !bell.contains(event.target)) {
                dropdown.style.display = 'none';
              }
            });
            
            
            function backFeature(){
                // Get the current URL path
                const currentUrl = window.location.pathname;
                
                // Get the last part of the URL (e.g., 'add_panelist' from '/add_panelist')
                const lastSegment = currentUrl.substring(currentUrl.lastIndexOf('/') + 1);
        
                if(lastSegment == "tracking") {
                    window.location.href = "/dashboard";
                }
                
                else if (lastSegment == "create_courses") {
                    window.location.href = "/courses";
                } 
                
                else if (lastSegment == "create_specializations") {
                    window.location.href = "/specializations";
                } 
                
                else if (lastSegment == "create_sections") {
                    window.location.href = "/sections";
                } 
                
                else if (lastSegment == "create_groups") {
                    window.location.href = "/groups";
                }
                
                else if (lastSegment == "add_adviser") {
                    window.location.href = "/group_inner";
                } 
                
                else if (lastSegment == "add_panelist") {
                    window.location.href = "/group_inner";
                } 
                
                else if (lastSegment == "add_chairman") {
                    window.location.href = "/group_inner";
                }
                
                else if (lastSegment == "add_student") {
                    window.location.href = "/group_inner";
                } 
                
                else if (lastSegment == "group_inner") {
                    window.location.href = "/groups";
                } 
                
                else if (lastSegment == "groups") {
                    window.location.href = "/sections";
                } 
                
                else if (lastSegment == "sections") {
                      window.location.href = "<?php echo $redirectUrl; ?>";
                }
                
                else if (lastSegment == "specializations") {
                    window.location.href = "/courses";
                }
                
                else if (lastSegment == "courses") {
                    window.location.href = "/dashboard";
                }
                
                else if (lastSegment == "upload") {
                    window.location.href = "/dashboard";
                }
                
                else if (lastSegment == "class_view") {
                    window.location.href = "/dashboard";
                }
                
                else if (lastSegment == "edit_title_evaluation") {
                    
                    <?php

                        
                        unset($_SESSION["title"]);
                        unset($_SESSION["intro"]);
                        unset($_SESSION["background"]);
                        unset($_SESSION["importance"]);
                        unset($_SESSION["scope"]);
                        
                        //unsets the sessions in students editing the title evaluation forms, for performance reasons as it is LongTexts
                    ?>
                    
                    window.location.href = "/class_view";
                }
                
                else if (lastSegment == "answer_title_evaluation") {
                    <?php
                    
                        unset($_SESSION["evaluation"]);
                        unset($_SESSION["evalComment"]);
                        unset($_SESSION["title"]);

                        
                    ?>
                    
                    window.location.replace("/group_inner")
                } 
                
                //Invitations
                
                else if (lastSegment == "send_defense_invitation") {

                    window.location.replace("/edit_defense_invitation");
                }
                
                else if (lastSegment == "edit_defense_invitation") {
                    window.location.replace("/class_view");
                }
                
                else if (lastSegment == "answer_defense_invitation") {
                    
                    window.location.href = "/group_inner";
                } 
                
                
                //Capstone papers
                
                else if (lastSegment == "edit_capstone_paper") {
                    
                    window.location.replace("/class_view");
                } 
                
                else if (lastSegment == "answer_capstone_paper") {
                    
                    window.location.href = "/group_inner";
                } 
                
                
                else if (lastSegment == "create_templates") {
                    window.location.href = "/dashboard";
                }
                
                
                else if (lastSegment == "edit_defense") {
          
                    window.location.href = "/class_view";
                }
                
                else if (lastSegment == "answer_defense") {

                    window.location.href = "/group_inner";
                }
                
                
                //Profiles and Accounts
                
                else if (lastSegment == "profile") {
                    window.location.href = "/dashboard";
                }
                
                else if (lastSegment == "password") {
                    window.location.href = "/dashboard";
                } 
                
                else if (lastSegment == "accounts" || lastSegment == "accounts_faculty") {
                    window.location.href = "/dashboard";
                }
                
                
                else if (lastSegment == "defense_date") {
                    window.location.href = "/groups";
                }
                
                
                else if (lastSegment == "academicyear_editor") {
                    window.location.href = "/dashboard";
                }
                
                else if (lastSegment == "edit_courses" || lastSegment == "edit_specializations" || lastSegment == "edit_sections") {
                    window.location.href = "/dashboard";
                }
                
                else if (lastSegment == "action_logs") {
                    window.location.href = "/dashboard";
                }
                
                
                else if (lastSegment == "reports_coordinators" || lastSegment == "reports_panelists" || lastSegment == "reports_advisers" || lastSegment == "reports_titles" || lastSegment == "reports_defense") {
                    window.location.href = "/dashboard";
                }
                

                
                else {
                    console.log("No matching last URL segment for: " + lastSegment); // Optional default case for unmatched segments
                }
        
            }
        </script>
        
        <footer>
            <p>© Capstrack 2024</p>
        </footer>
    </body>
</html>