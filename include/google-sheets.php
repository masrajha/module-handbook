<?php
require plugin_dir_path(__FILE__) . '../vendor/autoload.php';

use Google\Client;
use Google\Service\Sheets;

$pathToJson = plugin_dir_path(__FILE__) . 'storied-precept-243308-adf6b0bb18cb.json';
$spreadsheetId = '1HOkLicWIffmT7UxTqd2XNWKz5CmCLL5Ue9gO9rq5iBA';

function initialize_sheets_service() {
    global $pathToJson;
    $client = new Client();
    $client->setApplicationName('Google Sheets API PHP');
    $client->setScopes(Sheets::SPREADSHEETS_READONLY);
    $client->setAuthConfig($pathToJson);
    return new Sheets($client);
}

function get_courses() {
    global $spreadsheetId;
    $range = 'MODULES-HANDBOOK!A2:T';
    $service = initialize_sheets_service();

    try {
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();
        $dataByClass = [];

        if (!empty($values)) {
            foreach ($values as $row) {
                if (isset($row[19])) {
                    $sem = $row[19];
                    if (!isset($dataByClass[$sem])) {
                        $dataByClass[$sem] = [];
                    }
                    $dataByClass[$sem][] = $row;
                }
            }
        }

        $output = '';
        if (!empty($dataByClass)) {
            foreach ($dataByClass as $sem => $rows) {
                $output .= '<br/><h3>Semester ' . htmlspecialchars($sem, ENT_QUOTES, 'UTF-8') . '</h3>';
                $output .= '<table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Course</th>
                                        <th>Credits</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>';
                foreach ($rows as $row) {
                    $output .= '<tr>
                                    <td>' . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . '</td>
                                    <td>' . htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8') . '</td>
                                    <td>' . htmlspecialchars($row[9][0], ENT_QUOTES, 'UTF-8') . '</td>
                                    <td>
                                        <a href="?code=' . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . '"><i class="bi bi-book" title="Module Handbook"></i></a>
                                        <a href="?code=' . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . '&view=practice"><i class="bi bi-clipboard" title="Practice Instruction"></i></a>
                                        <a href="?code=' . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . '&view=project"><i class="bi bi-briefcase" title="Project Instruction"></i></a>
                                        <a href="?code=' . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . '&view=quiz"><i class="bi bi-question-circle" title="Quiz"></i></a>
                                    </td>
                                </tr>';
                }
                $output .= '</tbody></table>';
            }
        } else {
            $output .= '<div class="warning">Data is not found!</div>';
        }
        return $output;

    } catch (Exception $e) {
        return 'Caught exception: ' . $e->getMessage();
    }
}


function get_course_detail($code) {
    global $spreadsheetId;
    $range = 'MODULES-HANDBOOK!A2:T';
    $service = initialize_sheets_service();

    try {
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $data = null;
        if (!empty($values)) {
            foreach ($values as $row) {
                if ($row[0] == $code) {
                    $data = $row;
                    break;
                }
            }
        }

        if ($data) {
            $output = '<h2>MODULE HANDBOOK</h2>';
            $output .= '<h3>Bachelor of Computer Science</h3>
                        <table>
                            <tr>
                                <th>Module Name</th>
                                <td><b>' . htmlspecialchars($data[1], ENT_QUOTES, 'UTF-8') . '</b></td>
                            </tr>
                            <tr>
                                <th>Module Level</th>
                                <td>Undergraduate</td>
                            </tr>
                            <tr>
                                <th>Code</th>
                                <td>' . htmlspecialchars($data[0], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Course</th>
                                <td>' . htmlspecialchars($data[1], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Description</th>
                                <td>' . htmlspecialchars($data[17], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Semester</th>
                                <td>' . htmlspecialchars($data[2], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Lecturer</th>
                                <td>' . htmlspecialchars($data[4], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Contact Person</th>
                                <td>' . htmlspecialchars($data[3], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Language</th>
                                <td>' . htmlspecialchars($data[5], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Relation to Curriculum</th>
                                <td>' . htmlspecialchars($data[6], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Type of Teaching, Contact Hours</th>
                                <td>' . htmlspecialchars($data[7], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Workload</th>
                                <td>
                                    <ul>';
            $workload = explode("\n", htmlspecialchars($data[8], ENT_QUOTES, 'UTF-8'));
            foreach ($workload as $item) {
                if (!empty($item)) {
                    $output .= '<li>' . $item . '</li>';
                }
            }
            $output .= '</ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Credit Points</th>
                                <td>' . htmlspecialchars($data[9], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Requirements according to the Examination Regulations</th>
                                <td>' . htmlspecialchars($data[10], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Learning Outcomes (Course Outcomes) and Their Corresponding PLOs</th>
                                <td>
                                    <ol>';
            $learningOutcomes = explode("\n", htmlspecialchars($data[11], ENT_QUOTES, 'UTF-8'));
            foreach ($learningOutcomes as $outcome) {
                if (!empty($outcome)) {
                    $output .= '<li>' . $outcome . '</li>';
                }
            }
            $output .= '</ol>
                                </td>
                            </tr>
                            <tr>
                                <th>Competencies/Course Learning Outcomes</th>
                                <td>
                                    <ol>';
            $competencies = explode("\n", htmlspecialchars($data[18], ENT_QUOTES, 'UTF-8'));
            foreach ($competencies as $competency) {
                if (!empty($competency)) {
                    $output .= '<li>' . $competency . '</li>';
                }
            }
            $output .= '</ol>
                                </td>
                            </tr>
                            <tr>
                                <th>Contents</th>
                                <td>
                                    <ol>';
            $contents = explode("\n", htmlspecialchars($data[12], ENT_QUOTES, 'UTF-8'));
            foreach ($contents as $content) {
                if (!empty($content)) {
                    $output .= '<li>' . $content . '</li>';
                }
            }
            $output .= '</ol>
                                </td>
                            </tr>
                            <tr>
                                <th>Study and Examination Requirements and Forms of Examination</th>
                                <td>
                                    <ul>';
            $examRequirements = explode("\n", htmlspecialchars($data[13], ENT_QUOTES, 'UTF-8'));
            foreach ($examRequirements as $requirement) {
                if (!empty($requirement)) {
                    $output .= '<li>' . $requirement . '</li>';
                }
            }
            $output .= '</ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Media Employed</th>
                                <td>' . htmlspecialchars($data[14], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Assessments and Evaluation</th>
                                <td>
                                    <ul>';
            $assessments = explode("\n", htmlspecialchars($data[15], ENT_QUOTES, 'UTF-8'));
            foreach ($assessments as $assessment) {
                if (!empty($assessment)) {
                    $output .= '<li>' . $assessment . '</li>';
                }
            }
            $output .= '</ul>
                                </td>
                            </tr>
                            <tr>
                                <th>Reading List</th>
                                <td>
                                    <ol>';
            $readingList = explode("\n", htmlspecialchars($data[16], ENT_QUOTES, 'UTF-8'));
            foreach ($readingList as $item) {
                if (!empty($item)) {
                    $output .= '<li>' . $item . '</li>';
                }
            }
            $output .= '</ol>
                                </td>
                            </tr>
                        </table>';
        } else {
            $output = '<div class="warning">Data is not found!</div>';
        }

        return $output;

    } catch (Exception $e) {
        return 'Caught exception: ' . $e->getMessage();
    }
}

function get_practice($code) {
    global $spreadsheetId;
    $range = 'PRACTICE-INSTRUCTIONS!A2:E'; // Ganti dengan range yang sesuai
    $service = initialize_sheets_service();

    try {
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $dataById = [];
        if (!empty($values)) {
            foreach ($values as $row) {
                if (isset($row[0])) {
                    $id = $row[0];
                    if (!isset($dataById[$id])) {
                        $dataById[$id] = [];
                    }
                    $dataById[$id][] = $row;
                }
            }
        }

        $selectedData = isset($dataById[$code]) ? $dataById[$code] : [];

        if (!empty($selectedData)) {
            $output = '<h2>PRACTICE INSTRUCTION</h2>';
            $output .= '<h3>Course: ' . htmlspecialchars($selectedData[0][1], ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($selectedData[0][0], ENT_QUOTES, 'UTF-8') . ')</h3>';
            $output .= '<table>
                            <thead>
                                <tr>
                                    <th>Topics</th>
                                    <th>Instructions</th>
                                </tr>
                            </thead>
                            <tbody>';
            foreach ($selectedData as $row) {
                $output .= '<tr>
                                <td>' . htmlspecialchars($row[3], ENT_QUOTES, 'UTF-8') . '</td>
                                <td>
                                    <ol>';
                $instList = explode("\n", htmlspecialchars($row[4], ENT_QUOTES, 'UTF-8'));
                foreach ($instList as $item) {
                    if (!empty($item)) {
                        $output .= '<li>' . $item . '</li>';
                    }
                }
                $output .= '</ol>
                                </td>
                            </tr>';
            }
            $output .= '</tbody></table>';
        } else {
            $output = '<div class="warning">Data is not found or this course does not have a practice!</div>';
        }

        return $output;

    } catch (Exception $e) {
        return 'Caught exception: ' . $e->getMessage();
    }
}

function get_project($code) {
    global $spreadsheetId;
    $range = 'PROJECT-BASE INSTRUCTIONS!A2:E'; // Ganti dengan range yang sesuai
    $service = initialize_sheets_service();

    try {
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $data = null;
        if (!empty($values)) {
            foreach ($values as $row) {
                if ($row[0] == $code) {
                    $data = $row;
                    break;
                }
            }
        }

        if ($data) {
            $output = '<h2>PROJECT INSTRUCTION</h2>';
            $output .= '<h3>Course: ' . htmlspecialchars($data[1], ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($data[0], ENT_QUOTES, 'UTF-8') . ')</h3>
                        <table>
                            <tr>
                                <th>Objective</th>
                                <td>' . htmlspecialchars($data[2], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Case Study</th>
                                <td>' . htmlspecialchars($data[3], ENT_QUOTES, 'UTF-8') . '</td>
                            </tr>
                            <tr>
                                <th>Questions</th>
                                <td>
                                    <ol>';
            $questionList = explode("\n", htmlspecialchars($data[4], ENT_QUOTES, 'UTF-8'));
            foreach ($questionList as $item) {
                if (!empty($item)) {
                    $output .= '<li>' . $item . '</li>';
                }
            }
            $output .= '</ol>
                                </td>
                            </tr>
                            <tr>
                                <th>Grading</th>
                                <td>
                                    <ol>';
            $gradingList = explode("\n", htmlspecialchars($data[5], ENT_QUOTES, 'UTF-8'));
            foreach ($gradingList as $item) {
                if (!empty($item)) {
                    $output .= '<li>' . $item . '</li>';
                }
            }
            $output .= '</ol>
                                </td>
                            </tr>
                        </table>';
        } else {
            $output = '<div class="warning">Data is not found or this course does not have a project!</div>';
        }

        return $output;

    } catch (Exception $e) {
        return 'Caught exception: ' . $e->getMessage();
    }
}

function get_quiz($code) {
    global $spreadsheetId;
    $range = 'QUESTIONS BANK!A2:E'; // Ganti dengan range yang sesuai
    $service = initialize_sheets_service();

    try {
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $dataById = [];
        if (!empty($values)) {
            foreach ($values as $row) {
                if (isset($row[0])) {
                    $id = $row[0];
                    if (!isset($dataById[$id])) {
                        $dataById[$id] = [];
                    }
                    $dataById[$id][] = $row;
                }
            }
        }

        $selectedData = isset($dataById[$code]) ? $dataById[$code] : [];

        $midTerm = [];
        $examTerm = [];
        $contents = [];
        $uniqueContents = [];

        if (!empty($selectedData)) {
            $totalQuestions = count($selectedData);
            $midTermCount = (int)floor($totalQuestions / 2);

            // Mengumpulkan soal dan konten
            foreach ($selectedData as $question) {
                $soal = $question[3];
                $konten = $question[2];
                $code = $question[0];
                $course = $question[1];

                if (!isset($contents[$konten])) {
                    $contents[$konten] = "c-" . (count($contents) + 1);
                }

                if (count($midTerm) < $midTermCount) {
                    $midTerm[] = ['soal' => $soal, 'konten' => $konten];
                } else {
                    $examTerm[] = ['soal' => $soal, 'konten' => $konten];
                }
            }

            // Mengumpulkan konten unik
            foreach ($contents as $konten => $kode) {
                $uniqueContents[$kode] = $konten;
            }
        }

        if (!empty($selectedData)) {
            $output = '<h2>Question Examples</h2>';
            $output .= '<h3>Course: ' . htmlspecialchars($course, ENT_QUOTES, 'UTF-8') . ' (' . htmlspecialchars($code, ENT_QUOTES, 'UTF-8') . ')</h3>
                        <hr>
                        <h3>Exam Instructions</h3>
                        <ul>
                            <li>Please do not communicate with your classmates during the exam. All work must be completed independently.</li>
                            <li>If you are caught cheating, your exam will be immediately invalidated and you will receive a zero.</li>
                            <li>You are not allowed to use any electronic devices or outside materials during the exam.</li>
                            <li>If you have any questions about the exam, please raise your hand and ask the proctor for clarification.</li>
                            <li>Any form of cheating, including plagiarism, will not be tolerated and will result in disciplinary action.</li>
                            <li>Any attempt to cheat will be reported and may result in a failing grade for this course.</li>
                            <li>As believers in our religion, it is our duty to be truthful and honest in all our actions, including during this exam.</li>
                        </ul>
                        <hr>
                        <h3>Midterm Exam</h3>
                        <ol>';
            foreach ($midTerm as $item) {
                $output .= '<li>[c-' . htmlspecialchars($contents[$item['konten']], ENT_QUOTES, 'UTF-8') . '] ' . htmlspecialchars($item['soal'], ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $output .= '</ol>
                        <h3>Final Exam</h3>
                        <ol>';
            foreach ($examTerm as $item) {
                $output .= '<li>[c-' . htmlspecialchars($contents[$item['konten']], ENT_QUOTES, 'UTF-8') . '] ' . htmlspecialchars($item['soal'], ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $output .= '</ol>
                        <hr>
                        <h3>Contents of Course</h3>
                        <ol>';
            foreach ($uniqueContents as $kode => $konten) {
                $output .= '<li class="content-item">[c-' . htmlspecialchars($kode, ENT_QUOTES, 'UTF-8') . '] ' . htmlspecialchars($konten, ENT_QUOTES, 'UTF-8') . '</li>';
            }
            $output .= '</ol>';
        } else {
            $output = '<div class="warning">Data is not found!</div>';
        }

        return $output;

    } catch (Exception $e) {
        return 'Caught exception: ' . $e->getMessage();
    }
}
