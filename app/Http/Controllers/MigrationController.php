<?php

namespace app\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use app\Models\Client; 
use app\Models\User;
use app\Models\File;
use app\Models\Country;
use app\Models\Assessment;
use app\Models\AssessmentAttempt;
use app\Models\Question;
use app\Models\QuestionOption;
use app\Models\AssessmentAttemptAnswer;

use app\Models\TableMigration;

use app\Enums\FilePurpose;

use app\Services\UtilityService;

use app\Utilities;

class MigrationController extends Controller
{
    private $assessmentsMigration;
    private $questionsMigration;
    private $questionOptionsMigration;
    private $staffAssessmentsMigration;
    private $staffAssessmentAnswersMigration;

    public function __construct()
    {
        $this->assessmentsMigration = TableMigration::where("name", "assessments")->first();
        $this->questionsMigration = TableMigration::where("name", "questions")->first();
        $this->questionOptionsMigration = TableMigration::where("name", "question_options")->first();
        $this->staffAssessmentsMigration =  TableMigration::where("name", "virtual_staff_assessments")->first();
        $this->staffAssessmentAnswersMigration =  TableMigration::where("name", "virtual_staff_assessment_answers")->first();
    }
    
    public function index()
    {
        try{
            if(!$this->assessmentsMigration->migrated) $this->assessments();
            if(!$this->questionsMigration->migrated) $this->questions();
            if(!$this->questionOptionsMigration->migrated) $this->questionOptions();
            if(!$this->staffAssessmentsMigration->migrated) $this->assessmentAttempts();
        }catch(\Exception $e) {
            return Utilities::error($e, 'An error occurred while trying to process the request');
        }
    }

    private function markAsMigrated($migration) {
        $migration->migrated = true;
        $migration->update();
    }

    public function clients()
    {
        $sourceTable = 'customers'; // table in v1

        // Fetch from v1 in chunks (to handle large data)
        DB::connection('db1')->table($sourceTable)->orderBy('id')->chunk(500, function ($records) {
            foreach ($records as $record) {
                // Convert to array
                $data = (array) $record;

                $countryId = null;
                if($data['country_id']) {
                    $countryRecord = DB::connection('db1')->table('countries')->where('id', $data['country_id'])->first();
                    $countryRecord = (array) $countryRecord;
                    if($countryRecord) {
                        $country = Country::where("name", $countryRecord['name'])->first();
                        if($country) $countryId = $country->id;
                    }
                }

                // Use Eloquent model for v2
                $client = new Client;
                $client->title = $data['title'];
                $client->firstname = $data['firstname'];
                $client->lastname = $data['lastname'];
                // $client->othernames
                $client->email = $data['email'];
                $client->password = $data['password'];
                $client->phone_number = $data['phone_number'];
                // $client->photo_id 
                // $client->client_identification_id
                $client->dob = $data['dob'];
                $client->gender = $data['gender'];
                $client->provider_id = $data['provider_id'];
                $client->provider_name = $data['provider_name'];
                $client->email_verified_at = $data['email_verified_at'];
                $client->country_id = $countryId;
                // $client->state_id 
                $client->age_group_id = $data['age_group_id'];
                $client->address = $data['address'];
                $client->postal_code = $data['postal_code'];
                $client->marital_status = $data['marital_status'];
                $client->employment_status = $data['employment_status'];
                $client->occupation = $data['occupation'];
                $client->activated = $data['activated'];
                // $client->kyc_status = 
                $client->referer_id = $data['referer_id'];
                if($data['referer_id']) $client->referer_type = User::$userType;
                // $client->referer_code
                $client->migrated = true;

                $client->save();

                $photo = null;

                if($data['photo_id']) {
                    $photoRecord = DB::connection('db1')->table('files')->where('id', $data['photo_id'])->first();
                    $photoRecord = (array) $photoRecord;
                    if($photoRecord) {
                        $user = ["id" => $client->id, "type" => Client::$userType];
                        $file = $this->migrateFile($photoRecord, $user, $user, FilePurpose::CLIENT_PROFILE_PHOTO);
                        if($file) {
                            $client->photo_id = $file->id;
                            $client->update();
                        }
                    }
                }

                // User::updateOrCreate(
                //     ['id' => $data['id']], // match by id
                //     $data                  // fill attributes
                // );
            }
        });

        return response()->json(['message' => 'Clients copied successfully!']);
    }

    public function assessments()
    {
        dd('dont get here');
        $sourceTable = 'assessments'; // table in v1
        try{
            DB::beginTransaction();
            // Fetch from v1 in chunks (to handle large data)
            DB::connection('db1')->table($sourceTable)->orderBy('id')->chunk(500, function ($records) {
                foreach ($records as $record) {
                    // Convert to array
                    $data = (array) $record;

                    // Use Eloquent model for v2
                    $assessment = new Assessment;
                    $assessment->title = $data['title'];
                    $assessment->description = $data['description'];
                    $assessment->active = $data['active'];
                    $assessment->created_at = $data['created_at'];
                    $assessment->updated_at = $data['updated_at'];
                    $assessment->migrated = true;

                    $assessment->save();
                }
            });

            $this->markAsMigrated($this->assessmentsMigration);
            DB::commit();
            // return response()->json(['message' => 'Assessments copied successfully!']);
        }catch(\Exception $e) {
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request');
        }
    }

    public function questions()
    {
        dd('dont get here');
        // Fetch from v1 in chunks (to handle large data)
        try{
            DB::beginTransaction();
            DB::connection('db1')->table('questions')->orderBy('id')->chunk(500, function ($records) {
                if(count($records) > 0) {
                    foreach ($records as $record) {
                        $assessment = Assessment::where("migrated", true)->first();

                        if($assessment) {
                            // Convert to array
                            $data = (array) $record;

                            // Use Eloquent model for v2
                            $question = new Question;
                            $question->assessment_id = $assessment->id;
                            $question->question = $data['question'];
                            $question->migrated = true;

                            $question->save();
                        }
                    }
                }
            });
            $this->markAsMigrated($this->questionsMigration);
            DB::commit();
            // return response()->json(['message' => 'Assessment Questions copied successfully!']);
        }catch(\Exception $e) {
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request');
        }

    }

    public function questionOptions()
    {
        dd('dont get here');
        // Fetch from v1 in chunks (to handle large data)
        try{
            DB::beginTransaction();
            DB::connection('db1')->table('question_options')->orderBy('id')->chunk(500, function ($records) {
                if(count($records) > 0) {
                    foreach ($records as $record) {
                        
                        $data = (array) $record;

                        $v1Question = DB::connection('db1')->table('questions')->where("id", $data['question_id'])->first();
                        if($v1Question) {
                            $question = Question::where("question", $v1Question->question)->first();

                            if($question) {
                                // Use Eloquent model for v2
                                $option = new QuestionOption;
                                $option->question_id = $question->id;
                                $option->value = $data['value'];
                                $option->answer = $data['answer'];
                                $question->migrated = true;

                                $question->save();
                            }
                        }
                    }
                }
            });
            $this->markAsMigrated($this->questionOptionsMigration);
            DB::commit();
            // return response()->json(['message' => 'Assessment Questions copied successfully!']);
        }catch(\Exception $e) {
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request');
        }
    }

    public function assessmentAttempts()
    {
        // Fetch from v1 in chunks (to handle large data)
        try{
            DB::beginTransaction();
            DB::connection('db1')->table('virtual_staff_assessments')->orderBy('id')->chunk(500, function ($records) {
                if(count($records) > 0) {
                    foreach ($records as $record) {
                        $assessment = Assessment::where("migrated", true)->first();

                        if($assessment) {
                            // Convert to array
                            $data = (array) $record;

                            // Use Eloquent model for v2
                            $attempt = new AssessmentAttempt;
                            $attempt->assessment_id = $assessment->id;
                            $attempt->firstname = $data['firstname'];
                            $attempt->surname = $data['lastname'];
                            $attempt->email = $data['email'];
                            $attempt->phone_number = $data['phone_number'];
                            $attempt->address = $data['address'];
                            $attempt->gender = $data['gender'];
                            $attempt->occupation = $data['occupation'];
                            $attempt->referral_code = $data['referal_code'];
                            $attempt->score = $data['score'];
                            // $attempt->cut_off_mark = $data['cut_off_mark'];
                            $attempt->passed = $data['passed'];
                            // $attempt->correct = $data['correct'];
                            // $attempt->total_questions = $data['total_questions'];
                            // $attempt->started_at = $data['started_at'];
                            // $attempt->time_used = $data['time_used'];
                            $attempt->cancelled = ($data['status'] == -1) ? 1 : 0;
                            // $attempt->disqualified = $data['disqualified'];
                            $attempt->created_at = $data['created_at'];
                            $attempt->updated_at = $data['updated_at'];
                            $attempt->migrated = true;

                            $attempt->save();

                            $this->attemptAnswers($data['id'], $attempt->id);
                        }
                    }
                }
            });
            $this->markAsMigrated($this->staffAssessmentsMigration);
            DB::commit();
            // return response()->json(['message' => 'Assessment Attempts copied successfully!']);
        }catch(\Exception $e) {
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request');
        }
    }

    public function attemptAnswers($staffAssessmentId, $attemptId)
    {
        try{
            DB::beginTransaction();
            // Fetch from v1 in chunks (to handle large data)
            DB::connection('db1')->table('virtual_staff_assessment_answers')->where("virtual_staff_assessment_id", $staffAssessmentId)->orderBy('id')->chunk(500, function ($records) use($attemptId) {
                if(count($records) > 0) {
                    foreach ($records as $record) {
                        
                        $data = (array) $record;

                        $question = Question::where("question", $data['question'])->first();

                        if($question) {

                            $attemptAnswer = new AssessmentAttemptAnswer;

                            $attemptAnswer->attempt_id = $attemptId;
                            $attemptAnswer->question = $data['question'];
                            $attemptAnswer->question_id = $question->id;
                            $attemptAnswer->answer = $data['answer'];
                            $attemptAnswer->correct_answer = $data['correct_answer'];
                            $attemptAnswer->correct = $data['correct'];
                            $attemptAnswer->created_at = $data['created_at'];
                            $attemptAnswer->updated_at = $data['updated_at'];
                            $attemptAnswer->migrated = true;

                            $attemptAnswer->save();
                        }
                    }
                }
            });
            $this->markAsMigrated($this->staffAssessmentAnswersMigration);
            DB::commit();
            // return response()->json(['message' => 'Assessment Attempt Answers copied successfully!']);
        }catch(\Exception $e) {
            DB::rollBack();
            return Utilities::error($e, 'An error occurred while trying to process the request');
        }
    }

    private function migrateFile($record, $user, $belongs, $purpose)
    {
        $file = new File;
        $file->user_id = $user['id'];
        $file->user_type = $user['type'];
        $file->file_type = $record['file_type'];
        $file->mime_type = $record['mime_type'];
        $file->filename = $record['filename'];
        $file->original_filename = $record['original_filename'];
        $file->extension = $record['extension'];
        $file->size = $record['size'];
        $file->formatted_size = $record['formatted_size'];
        $file->url = $record['url'];
        $file->belongs_id = $belongs['id'];
        $file->belongs_type = $belongs['type'];
        $file->purpose = $purpose;
        $file->public_id = $record['public_id'];
        $file->width = $record['width'];
        $file->height = $record['height'];
        $file->migrated = true;
        $file->save();

        return $file;
    }


}
