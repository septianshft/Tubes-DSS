# UML Diagrams for Scholarship Management System

This document contains various UML diagrams for the scholarship management system using PlantUML format.

## 1. Entity Relationship Diagram (ERD)

```plantuml
@startuml ERD
!define TABLE(name,desc) class name as "desc" << (T,#FFAAAA) >>
!define PRIMARY_KEY(x) <u>x</u>
!define FOREIGN_KEY(x) <i>x</i>

TABLE(users, "users") {
  PRIMARY_KEY(id): bigint
  name: varchar(255)
  email: varchar(255)
  email_verified_at: timestamp
  password: varchar(255)
  remember_token: varchar(100)
  created_at: timestamp
  updated_at: timestamp
}

TABLE(students, "students") {
  PRIMARY_KEY(id): bigint
  FOREIGN_KEY(teacher_id): bigint
  name: varchar(255)
  nisn: varchar(10)
  email: varchar(255)
  phone: varchar(255)
  address: text
  date_of_birth: date
  extracurricular_position: varchar(255)
  extracurricular_activeness: integer
  class_attendance_percentage: decimal(5,2)
  average_score: decimal(5,2)
  tuition_payment_delays: integer
  created_at: timestamp
  updated_at: timestamp
}

TABLE(scholarship_batches, "scholarship_batches") {
  PRIMARY_KEY(id): bigint
  name: varchar(255)
  description: text
  start_date: date
  end_date: date
  quota: integer
  is_active: boolean
  created_at: timestamp
  updated_at: timestamp
}

TABLE(student_submissions, "student_submissions") {
  PRIMARY_KEY(id): bigint
  FOREIGN_KEY(student_id): bigint
  FOREIGN_KEY(scholarship_batch_id): bigint
  submission_date: timestamp
  status: enum
  saw_score: decimal(8,4)
  rank_position: integer
  created_at: timestamp
  updated_at: timestamp
}

TABLE(model_has_roles, "model_has_roles") {
  FOREIGN_KEY(role_id): bigint
  model_type: varchar(255)
  FOREIGN_KEY(model_id): bigint
}

TABLE(roles, "roles") {
  PRIMARY_KEY(id): bigint
  name: varchar(255)
  guard_name: varchar(255)
  created_at: timestamp
  updated_at: timestamp
}

users ||--o{ students : "teacher_id"
students ||--o{ student_submissions : "student_id"
scholarship_batches ||--o{ student_submissions : "scholarship_batch_id"
users ||--o{ model_has_roles : "model_id"
roles ||--o{ model_has_roles : "role_id"

@enduml
```

## 2. Use Case Diagram

```plantuml
@startuml UseCase
left to right direction
skinparam packageStyle rectangle

actor "Admin" as admin
actor "Teacher" as teacher
actor "Student" as student

rectangle "Scholarship Management System" {
  usecase "Login" as UC1
  usecase "Manage Users" as UC2
  usecase "Manage Scholarship Batches" as UC3
  usecase "View System Reports" as UC4
  
  usecase "Manage Students" as UC5
  usecase "Create Student" as UC6
  usecase "Edit Student" as UC7
  usecase "Delete Student" as UC8
  usecase "View Student List" as UC9
  usecase "Submit Student for Scholarship" as UC10
  usecase "View Scholarship Results" as UC11
  
  usecase "View Profile" as UC12
  usecase "View Scholarship Status" as UC13
  usecase "Update Personal Info" as UC14
  
  usecase "Calculate SAW Score" as UC15
  usecase "Generate Rankings" as UC16
  usecase "Export Results" as UC17
}

admin --> UC1
admin --> UC2
admin --> UC3
admin --> UC4
admin --> UC17

teacher --> UC1
teacher --> UC5
teacher --> UC6
teacher --> UC7
teacher --> UC8
teacher --> UC9
teacher --> UC10
teacher --> UC11

student --> UC1
student --> UC12
student --> UC13
student --> UC14

UC5 .> UC6 : includes
UC5 .> UC7 : includes
UC5 .> UC8 : includes
UC5 .> UC9 : includes

UC10 .> UC15 : includes
UC11 .> UC16 : includes

@enduml
```

## 3. Sequence Diagram - Student Edit Process

```plantuml
@startuml SequenceEditStudent
actor Teacher
participant "Web Browser" as Browser
participant "EditStudent\nLivewire Component" as Component
participant "EnsureStudentOwnership\nMiddleware" as Middleware
participant "Student Model" as Model
participant Database

Teacher -> Browser: Navigate to edit student page
Browser -> Middleware: Request with student ID
Middleware -> Database: Check teacher owns student
Database -> Middleware: Return ownership result

alt Student belongs to teacher
    Middleware -> Component: Allow access
    Component -> Model: Load student data
    Model -> Database: SELECT student
    Database -> Model: Return student data
    Model -> Component: Student object
    Component -> Browser: Render edit form
    Browser -> Teacher: Display form with data
    
    Teacher -> Browser: Fill form and submit
    Browser -> Component: Submit form data
    Component -> Component: Validate input
    
    alt Validation passes
        Component -> Model: Update student
        Model -> Database: UPDATE student
        Database -> Model: Success
        Model -> Component: Updated
        Component -> Browser: Success message & redirect
        Browser -> Teacher: Show success & redirect
    else Validation fails
        Component -> Browser: Show validation errors
        Browser -> Teacher: Display errors
    end
    
else Student doesn't belong to teacher
    Middleware -> Browser: Return 403 Forbidden
    Browser -> Teacher: Access denied
end

@enduml
```

## 4. Sequence Diagram - Scholarship Submission Process

```plantuml
@startuml SequenceScholarshipSubmission
actor Teacher
participant "Web Interface" as Web
participant "SubmissionController" as Controller
participant "SAWCalculatorService" as SAW
participant "StudentSubmission\nModel" as Submission
participant "Student Model" as Student
participant Database

Teacher -> Web: Select students for scholarship
Web -> Controller: Submit student list
Controller -> Student: Validate student ownership
Student -> Database: Check teacher_id
Database -> Student: Return validation result

alt Students belong to teacher
    loop For each student
        Controller -> SAW: Calculate SAW score
        SAW -> Student: Get student criteria data
        Student -> Database: Fetch student data
        Database -> Student: Return criteria values
        Student -> SAW: Return student data
        SAW -> SAW: Apply SAW algorithm
        SAW -> Controller: Return calculated score
        
        Controller -> Submission: Create submission
        Submission -> Database: INSERT submission with score
        Database -> Submission: Success
    end
    
    Controller -> SAW: Generate rankings
    SAW -> Database: UPDATE rank positions
    Database -> SAW: Success
    SAW -> Controller: Rankings complete
    
    Controller -> Web: Success response
    Web -> Teacher: Show success message
    
else Students don't belong to teacher
    Controller -> Web: Access denied
    Web -> Teacher: Show error
end

@enduml
```

## 5. Activity Diagram - Student Management Process

```plantuml
@startuml ActivityStudentManagement
start

:Teacher logs in;
:Navigate to Students section;

if (Action selected?) then (Create)
    :Fill student form;
    :Validate input;
    if (Valid?) then (yes)
        :Save new student;
        :Show success message;
    else (no)
        :Show validation errors;
        stop
    endif
    
elseif (Edit) then
    :Select student to edit;
    :Check ownership;
    if (Owns student?) then (yes)
        :Load student data;
        :Display edit form;
        :Modify student data;
        :Validate changes;
        if (Valid?) then (yes)
            :Update student;
            :Show success message;
        else (no)
            :Show validation errors;
            stop
        endif
    else (no)
        :Show access denied;
        stop
    endif
    
elseif (Delete) then
    :Select student to delete;
    :Check ownership;
    if (Owns student?) then (yes)
        :Confirm deletion;
        if (Confirmed?) then (yes)
            :Delete student;
            :Show success message;
        else (no)
            :Cancel operation;
        endif
    else (no)
        :Show access denied;
        stop
    endif
    
else (View List)
    :Load teacher's students;
    :Display student list;
endif

:Return to dashboard;
stop

@enduml
```

## 6. Activity Diagram - SAW Calculation Process

```plantuml
@startuml ActivitySAWCalculation
start

:Receive student data for scholarship;
:Initialize SAW Calculator;

partition "Data Preparation" {
    :Extract criteria values;
    note right: extracurricular_activeness,\nclass_attendance_percentage,\naverage_score, etc.
    
    :Normalize criteria values;
    note right: Convert to 0-1 scale
    
    :Apply criteria weights;
    note right: Each criterion has\npredefined weight
}

partition "SAW Calculation" {
    :Calculate weighted sum;
    note right: SAW Score = Σ(weight × normalized_value)
    
    :Store SAW score;
}

partition "Ranking" {
    :Collect all submissions;
    :Sort by SAW score (descending);
    :Assign rank positions;
    :Update database with ranks;
}

:Return calculated results;
stop

@enduml
```

## 7. Class Diagram - Core Models

```plantuml
@startuml ClassDiagram
class User {
    +id: int
    +name: string
    +email: string
    +password: string
    +email_verified_at: timestamp
    +created_at: timestamp
    +updated_at: timestamp
    --
    +students(): HasMany
    +hasRole(role): bool
}

class Student {
    +id: int
    +teacher_id: int
    +name: string
    +nisn: string
    +email: string
    +phone: string
    +address: string
    +date_of_birth: date
    +extracurricular_position: string
    +extracurricular_activeness: int
    +class_attendance_percentage: decimal
    +average_score: decimal
    +tuition_payment_delays: int
    +created_at: timestamp
    +updated_at: timestamp
    --
    +teacher(): BelongsTo
    +submissions(): HasMany
    +getFormattedDateOfBirth(): string
}

class ScholarshipBatch {
    +id: int
    +name: string
    +description: text
    +start_date: date
    +end_date: date
    +quota: int
    +is_active: bool
    +created_at: timestamp
    +updated_at: timestamp
    --
    +submissions(): HasMany
    +isActive(): bool
    +isOpen(): bool
}

class StudentSubmission {
    +id: int
    +student_id: int
    +scholarship_batch_id: int
    +submission_date: timestamp
    +status: enum
    +saw_score: decimal
    +rank_position: int
    +created_at: timestamp
    +updated_at: timestamp
    --
    +student(): BelongsTo
    +scholarshipBatch(): BelongsTo
    +isWinner(): bool
}

class SAWCalculatorService {
    -weights: array
    --
    +calculateScore(student): float
    +normalizeValue(value, type): float
    +generateRankings(submissions): void
    +exportResults(batchId): array
}

class EditStudent {
    +student: Student
    +name: string
    +nisn: string
    +email: string
    +phone: string
    +address: string
    +date_of_birth: string
    +extracurricular_position: string
    +extracurricular_activeness: int
    +class_attendance_percentage: float
    +average_score: float
    +tuition_payment_delays: int
    --
    +mount(student): void
    +rules(): array
    +update(): void
    +render(): View
}

User ||--o{ Student : teacher_id
Student ||--o{ StudentSubmission : student_id
ScholarshipBatch ||--o{ StudentSubmission : scholarship_batch_id
SAWCalculatorService ..> StudentSubmission : calculates
SAWCalculatorService ..> Student : uses
EditStudent --> Student : edits

@enduml
```

## 8. Activity Diagram - Teacher Workflow: Student Data to Scholarship Assignment

```plantuml
@startuml ActivityTeacherWorkflow
title Teacher Workflow: From Student Data Input to Scholarship Assignment

start

:Teacher logs into system;

partition "Student Data Management" {
    :Access student management section;
    
    repeat
        :Create new student record;
        :Input student personal data;
        note right: Name, NISN, email, phone,\naddress, date of birth
        
        :Input academic criteria;
        note right: Average score,\nclass attendance percentage
        
        :Input extracurricular data;
        note right: Position, activeness level
        
        :Input financial criteria;
        note right: Tuition payment delays
        
        :Validate student data;
        
        if (Data valid?) then (yes)
            :Save student record;
            :Show success confirmation;
        else (no)
            :Display validation errors;
            :Correct invalid data;
        endif
        
    repeat while (More students to add?) is (yes)
    -> no;
}

partition "Scholarship Assignment" {
    :Check available scholarship batches;
    
    if (Active batches available?) then (yes)
        :Select scholarship batch;
        :View batch details and requirements;
        
        :Select eligible students;
        note right: Review student criteria\nagainst batch requirements
        
        :Review selected students;
        
        if (Students meet criteria?) then (yes)
            :Submit students for scholarship;
            :System calculates SAW scores;
            :Generate student rankings;
            :Display submission confirmation;
            
            :Notify students about submission;
            note right: Send email notifications\nto submitted students
            
        else (no)
            :Remove ineligible students;
            :Add notes for improvement;
        endif
        
    else (no)
        :Wait for admin to create batches;
        :Check periodically for new batches;
    endif
}

:Monitor scholarship results;
:Review student performance;

stop

@enduml
```

## 9. Activity Diagram - Admin Workflow: Batch Creation to Student Acceptance

```plantuml
@startuml ActivityAdminWorkflow
start

:Admin logs into system;

partition "Batch Creation" {
    :Access scholarship management;
    :Create new scholarship batch;
    
    :Define batch parameters;
    note right: Name, description,\nstart/end dates, quota
    
    :Set scholarship criteria weights;
    note right: Academic performance,\nextracurricular, attendance, etc.
    
    :Configure batch settings;
    note right: Application deadline,\neligibility requirements
    
    :Validate batch configuration;
    
    if (Configuration valid?) then (yes)
        :Save scholarship batch;
        :Activate batch for submissions;
        :Notify teachers about new batch;
    else (no)
        :Correct configuration errors;
    endif
}

partition "Submission Management" {
    :Monitor incoming submissions;
    
    repeat
        :Review submitted students;
        :Check SAW score calculations;
        :Verify teacher submissions;
        
        if (Deadline reached?) then (yes)
            :Stop accepting new submissions;
        else (no)
            :Continue monitoring;
        endif
        
    repeat while (Submission period active?) is (yes)
    -> no;
}

partition "Student Evaluation & Selection" {
    :Generate final rankings;
    :Review top-ranked students;
    
    :Apply additional criteria;
    note right: Manual review for\nspecial circumstances
    
    :Select scholarship recipients;
    note right: Based on quota and\nfinal rankings
    
    :Review selected students;
    
    if (Selection approved?) then (yes)
        :Mark students as accepted;
        :Update submission status;
        :Generate acceptance letters;
    else (no)
        :Revise selection criteria;
        :Re-evaluate candidates;
    endif
}

partition "Batch Closure" {
    :Notify all participants;
    note right: Send results to students\nand teachers
    
    :Generate batch report;
    note right: Statistics, rankings,\naccepted students list
    
    :Archive batch data;
    :Update system records;
    
    :Close scholarship batch;
    :Mark batch as completed;
    
    :Prepare for next batch cycle;
}

:Review system analytics;
:Plan future improvements;

stop

@enduml
```

## Usage Instructions

1. Copy each PlantUML code block (between the ```plantuml markers)
2. Paste it into the PlantUML online editor at https://plantuml.com/plantuml/
3. Click "Submit" to generate the diagram
4. You can export as PNG, SVG, or other formats

## Diagram Descriptions

- **ERD**: Shows the database structure and relationships between tables
- **Use Case**: Illustrates system functionality from user perspectives
- **Sequence (Edit Student)**: Shows the flow of editing a student record
- **Sequence (Scholarship)**: Shows the scholarship submission and SAW calculation process
- **Activity (Student Management)**: Shows the workflow for managing students
- **Activity (SAW Calculation)**: Shows the SAW algorithm execution flow
- **Activity (Teacher Workflow)**: Shows the complete teacher workflow from inputting student data to assigning them to scholarship batches
- **Activity (Admin Workflow)**: Shows the complete admin workflow from creating scholarship batches to accepting students and closing batches
- **Class Diagram**: Shows the object-oriented structure of core system components
