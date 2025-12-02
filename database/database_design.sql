-- ---------------------------------------- Module quản lý năm học ----------------------------------------
-- Bảng năm học
CREATE TABLE academic_years (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng học kỳ
CREATE TABLE semesters (
    id SERIAL PRIMARY KEY,
    academic_year_id INT,
    name VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (academic_year_id, name),
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE CASCADE
);

-- ---------------------------------------- Module quản lý tổ chức ----------------------------------------
-- Bảng loại tổ chức
-- Các loại: Trường, Liên chi đoàn, Chi đoàn, Câu lạc bộ, Hội, Đội, Nhóm, ...
-- is_exclusive: 1 người chỉ được sinh hoạt tại 1 chi đoàn 
CREATE TABLE org_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_exclusive BOOLEAN DEFAULT FALSE,
)

-- Bảng cấp tổ chức
-- Các cấp: Trường, Liên chi đoàn, Chi đoàn, Câu lạc bộ, Hội, Đội, Nhóm, ...
CREATE TABLE org_levels (
    id SERIAL PRIMARY KEY,
    equivalent_name VARCHAR(100) NOT NULL UNIQUE,
    level_index INT NOT NULL UNIQUE
);

-- Bảng tổ chức
CREATE TABLE organizations (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    parent_org_id INT,
    org_type_id INT NOT NULL,
    org_level_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_org_id) REFERENCES organizations(id) ON DELETE SET NULL,
    FOREIGN KEY (org_type_id) REFERENCES org_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (org_level_id) REFERENCES org_levels(id) ON DELETE RESTRICT,
);

-- Bảng liên kết người dùng sinh hoạt tại tổ chức nào (cần restrict chỉ được sinh hoạt tại 1 chi đoàn, tức là org_type = 'Chi đoàn' và org_level = 'Chi đoàn')
CREATE TABLE user_orgs (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    org_id INT NOT NULL,
    joined_at DATE NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, org_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (org_id) REFERENCES organizations(id) ON DELETE CASCADE
);

-- Bảng yêu cầu tham gia tổ chức
-- Chỉ có tổ chức is_exclusive = FALSE mới cần yêu cầu tham gia (tức là các loại câu lạc bộ, đội, nhóm, ...)
CREATE TABLE org_join_requests (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    org_id INT NOT NULL,
    status ENUM("pending", "approved", "rejected") NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, org_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (org_id) REFERENCES organizations(id) ON DELETE CASCADE
);

-- ---------------------------------------- Module quản lý người dùng và hồ sơ ----------------------------------------
-- Quản lý người dùng
-- Bảng loại người dùng
-- Các loại: Cán bộ giảng viên, sinh viên trong sư phạm, sinh viên ngoài sư phạm
CREATE TABLE user_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Bảng người dùng
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    user_type_id INT NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_type_id) REFERENCES user_types(id) ON DELETE RESTRICT
);

-- Bảng nhóm người dùng
CREATE TABLE user_groups (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255),
    is_public BOOLEAN DEFAULT TRUE,
    status ENUM('active', 'inactive') DEFAULT 'active',
    min_members INT DEFAULT 1,
    max_members INT DEFAULT 100,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)

-- Bảng vai trò trong nhóm người dùng
-- Các vai trò: Giảng viên hướng dẫn (chính), Giảng viên hướng dẫn (phụ), Trưởng nhóm, Phó nhóm, Thành viên
CREATE TABLE user_group_roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Bảng lưu thành viên nhóm
CREATE TABLE user_group_members (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    user_group_id INT NOT NULL,
    role_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    UNIQUE (user_id, user_group_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_group_id) REFERENCES user_groups(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES user_group_roles(id) ON DELETE RESTRICT
);

-- Bảng lưu yêu cầu tham gia nhóm
-- 1 thành viên có thể tham gia bằng cách nhập mật khẩu (sẽ tự động tham gia), yêu cầu tham gia (trưởng nhóm duyệt), được mời tham gia
CREATE TABLE user_group_join_requests (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    user_group_id INT NOT NULL,
    request_type ENUM('password', 'request', 'invitation') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, user_group_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (user_group_id) REFERENCES user_groups(id) ON DELETE CASCADE
);

-- Quản lý hồ sơ
-- Bảng hồ sơ người dùng
CREATE TABLE user_profiles (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    bio TEXT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female'),
    avatar_url VARCHAR(255),
    joined_academic_year_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    FOREIGN KEY (joined_academic_year_id) REFERENCES academic_years(id) ON DELETE SET NULL
);

-- Bảng loại liên hệ'
-- Facebook, Zalo, Email, Số điện thoại, Địa chỉ nhà riêng, Địa chỉ liên lạc khác, ...
CREATE TABLE contact_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    icon_url VARCHAR(255)
)

-- Bảng liên hệ người dùng
CREATE TABLE user_contacts (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    contact_type_id INT NOT NULL,
    contact_value VARCHAR(255) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (user_id, contact_type_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_type_id) REFERENCES contact_types(id) ON DELETE RESTRICT
);

-- Bảng lưu xếp loại học lực
-- Các loại: Xuất sắc, Giỏi, Khá, Trung bình, Yếu, Kém
CREATE TABLE academic_ranking_levels (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

-- Bảng lưu điểm học tập theo kì
CREATE TABLE academic_semester_results (
    id SERIAL PRIMARY KEY, 
    user_id INT NOT NULL,
    semester_id INT NOT NULL,
    gpa DECIMAL(3, 2) NOT NULL,
    credit_earned INT NOT NULL,
    credit_failed INT NOT NULL,
    subject_failed INT NOT NULL,
    warning_count INT NOT NULL,
    academic_ranking_level_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE RESTRICT,
    FOREIGN KEY (academic_ranking_level_id) REFERENCES academic_ranking_levels(id) ON DELETE SET NULL
)

-- Bảng lưu điểm học tập theo năm học
CREATE TABLE academic_year_results (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    gpa DECIMAL(3, 2) NOT NULL,
    credit_earned INT NOT NULL,
    credit_failed INT NOT NULL,
    subject_failed INT NOT NULL,
    warning_count INT NOT NULL,
    academic_ranking_level_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT,
    FOREIGN KEY (academic_ranking_level_id) REFERENCES academic_ranking_levels(id) ON DELETE SET NULL
)

-- Bảng lưu điểm rèn luyện
CREATE TABLE training_results (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    semester_id INT NOT NULL,
    form_id INT NOT NULL,
    score DECIMAL(4, 2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL,
    verifier_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE RESTRICT,
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE RESTRICT
    FOREIGN KEY (verifier_id) REFERENCES users(id) ON DELETE SET NULL
)

-- ----------------------------------------- Module quản lý hoạt động ----------------------------------------
-- Hoạt động chung
-- Bảng loại hoạt động
-- Các loại: Hoạt động cần phản hồi,  hoạt động không cần phản hồi
CREATE TABLE activity_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Bảng danh mục hoạt động
-- Các loại danh mục: NCKH, NVSP, Hoạt động Đoàn, Khác
CREATE TABLE activity_categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
    is_multi_level BOOLEAN DEFAULT FALSE
);

-- Bảng hoạt động
CREATE TABLE activities (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    org_id INT NOT NULL,
    time_type ENUM('Năm học', 'Học kì') NOT NULL,
    time_id INT NOT NULL,
    creator_id INT NOT NULL,
    parent_activity_id INT,
    submission_requirment_id INT,
    is_visible BOOLEAN DEFAULT TRUE,
    activity_type_id INT NOT NULL,
    activity_category_id INT NOT NULL,
    status ENUM('draft', 'verified', 'rejected') DEFAULT 'draft',
    progress ENUM('not_started', 'on_going', 'completed') DEFAULT 'not_started',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (org_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (parent_activity_id) REFERENCES activities(id) ON DELETE SET NULL,
    FOREIGN KEY (submission_requirment_id) REFERENCES submission_requirements(id) ON DELETE SET NULL,
    FOREIGN KEY (activity_type_id) REFERENCES activity_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (activity_category_id) REFERENCES activity_categories(id) ON DELETE RESTRICT
)

-- Bảng file đính kèm hoạt động
CREATE TABLE activity_attachments (
    id SERIAL PRIMARY KEY,
    activity_id INT NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Bảng vai trò trong hoạt động
-- Các vai trò: người tham gia, người tham dự, người tổ chức, trợ lý, người hỗ trợ,...
CREATE TABLE activity_roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Bảng phân công hoạt động
CREATE TABLE activity_assignments (
    id SERIAL PRIMARY KEY,
    activity_id INT NOT NULL,
    assigned_by_id INT NOT NULL,
    assigned_to_id INT NOT NULL,
    role_id INT NOT NULL,
    notes TEXT,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('not_started', 'on_going', 'finished') DEFAULT 'not_started',
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (assigned_to_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (role_id) REFERENCES activity_roles(id) ON DELETE RESTRICT
);

-- Hoạt động Đoàn
-- Bảng kế hoạch hoạt động năm học
CREATE TABLE annual_plans (
    id SERIAL PRIMARY KEY,
    org_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('draft', 'verified', 'rejected') DEFAULT 'draft',
    is_visible BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (org_id, academic_year_id),
    FOREIGN KEY (org_id) REFERENCES organizations(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT
);

-- Bảng hoạt động hàng năm
CREATE TABLE annual_activities (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_reusable BOOLEAN DEFAULT FALSE
);

-- Bảng liên kết hoạt động hàng năm trong kế hoạch hoạt động năm học
CREATE TABLE annual_plan_activities (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL, -- Tên sau khi đã custom lại 
    notes TEXT,
    annual_plan_id INT NOT NULL,
    annual_activity_id INT NOT NULL,
    scheduled_date DATE NOT NULL,
    UNIQUE (annual_plan_id, annual_activity_id),
    FOREIGN KEY (annual_plan_id) REFERENCES annual_plans(id) ON DELETE CASCADE,
    FOREIGN KEY (annual_activity_id) REFERENCES annual_activities(id) ON DELETE RESTRICT
);

-- Bảng loại hoạt động Đoàn
-- Các loại: Chính trị, Nghiệp vụ, Chuyên môn
CREATE TABLE doan_activity_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
)

-- Bảng hoạt động Đoàn
CREATE TABLE doan_activities (
    id SERIAL PRIMARY KEY,
    activity_id INT NOT NULL,
    doan_activity_type_id INT NOT NULL,
    annual_plan_activity_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (doan_activity_type_id) REFERENCES doan_activity_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (annual_plan_activity_id) REFERENCES annual_plan_activities(id) ON DELETE SET NULL
);

-- Nghiệp vụ sư phạm
-- Bảng loại hoạt động NVSP
-- Các loại: Phần thi, Tọa đàm, Lớp bồi dưỡng
CREATE TABLE nvsp_activity_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Bảng hoạt động nvsp
CREATE TABLE nvsp_activities (
    id SERIAL PRIMARY KEY,
    activity_id INT NOT NULL,
    nvsp_activity_type_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (nvsp_activity_type_id) REFERENCES nvsp_activity_types(id) ON DELETE RESTRICT
);

-- Nghiên cứu khoa học
-- Bảng hoạt động nghiên cứu khoa học
CREATE TABLE nckh_activities (
    id SERIAL PRIMARY KEY,
    activity_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
);

-- Bảng chủ đề nghiên cứu khoa học (gốc)
CREATE TABLE nckh_topics (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_reusable BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng chủ đề nghiên cứu khoa học (theo năm)
-- Ví dụ chủ đề gốc là AI thì năm nay là AI trong giáo dục, nhưng vẫn thuộc loại AI
CREATE TABLE nckh_topic_versions (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    nckh_topic_id INT NOT NULL,
    academic_year_id INT NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    UNIQUE (nckh_topic_id, academic_year_id),
    FOREIGN KEY (nckh_topic_id) REFERENCES nckh_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT
)

-- Bảng đề tài NCKH
CREATE TABLE nckh_projects (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    nckh_activity_id INT NOT NULL,
    nckh_topic_version_id INT NOT NULL,
    status ENUM('proposed', 'approved', 'in_progress', 'completed', 'rejected') DEFAULT 'proposed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nckh_activity_id) REFERENCES nckh_activities(id) ON DELETE CASCADE,
    FOREIGN KEY (nckh_topic_version_id) REFERENCES nckh_topic_versions(id) ON DELETE RESTRICT
);

-- Bảng nhóm tham gia NCKH
CREATE TABLE nckh_project_teams (
    id SERIAL PRIMARY KEY,
    nckh_project_id INT NOT NULL,
    team_name VARCHAR(255) NOT NULL,
    user_group_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nckh_project_id) REFERENCES nckh_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_group_id) REFERENCES user_groups(id) ON DELETE RESTRICT 
);

-- Bảng lưu nộp bài báo NCKH
CREATE TABLE nckh_project_submissions (
    id SERIAL PRIMARY KEY,
    nckh_project_id INT NOT NULL,
    remark TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    verifier_id INT,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nckh_project_id) REFERENCES nckh_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (verifier_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Bảng lưu file liên kết với bài báo
CREATE TABLE nckh_project_submission_files (
    id SERIAL PRIMARY KEY,
    nckh_project_submission_id INT NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nckh_project_submission_id) REFERENCES nckh_project_submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
);

-- Bảng chấm phản biện bài báo NCKH
CREATE TABLE nckh_project_reviews (
    id SERIAL PRIMARY KEY,
    nckh_project_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    comments TEXT,
    score DECIMAL(3, 2),
    form_id INT,
    reviewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nckh_project_id) REFERENCES nckh_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE RESTRICT
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE RESTRICT,
);

-- Bảng phân công phản biện
CREATE TABLE nckh_project_reviewer_assignments (
    id SERIAL PRIMARY KEY,
    nckh_project_id INT NOT NULL,
    assigned_by_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nckh_project_id) REFERENCES nckh_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Bảng tiểu ban
CREATE TABLE subcommittees (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    nckh_activity_id INT NOT NULL,
    max_participants INT,  -- Số lượng người tham dự tối đa
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (nckh_activity_id) REFERENCES nckh_activities(id) ON DELETE CASCADE
);

-- Bảng vai trò trong tiểu ban
CREATE TABLE subcommittee_roles (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Bảng thành viên trong tiểu ban 
CREATE TABLE subcommittee_members (
    id SERIAL PRIMARY KEY,
    subcommittee_id INT NOT NULL,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (subcommittee_id, user_id),
    FOREIGN KEY (subcommittee_id) REFERENCES subcommittees(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES subcommittee_roles(id) ON DELETE RESTRICT
);

-- Bảng gắn đề tài vào tiểu ban
CREATE TABLE nckh_project_subcommittee_assignments (
    id SERIAL PRIMARY KEY,
    nckh_project_id INT NOT NULL,
    subcommittee_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (nckh_project_id, subcommittee_id),
    FOREIGN KEY (nckh_project_id) REFERENCES nckh_projects(id) ON DELETE CASCADE,
    FOREIGN KEY (subcommittee_id) REFERENCES subcommittees(id) ON DELETE CASCADE
);

-- Phản hồi và nộp bài
-- Bảng yêu cầu minh chứng
CREATE TABLE submission_requirements (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    require_file_upload BOOLEAN DEFAULT TRUE,
    allowed_file_types VARCHAR(255), -- CSV danh sách các định dạng file được phép, ví dụ: "pdf, docx, jpg"
    max_file_size_mb INT DEFAULT 10, -- Kích thước file tối đa tính theo MB
    require_text_input BOOLEAN DEFAULT FALSE,
    require_link_input BOOLEAN DEFAULT FALSE,
    max_text_length INT DEFAULT 2000,
    max_link_length INT DEFAULT 500,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng minh chứng
CREATE TABLE submissions (
    id SERIAL PRIMARY KEY,
    activity_id INT NOT NULL,
    submitter_id INT NOT NULL,
    remarks TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    verifier_id INT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (submitter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verifier_id) REFERENCES users(id) ON DELETE SET NULL
)

-- Bảng lưu file liên kết với minh chứng
CREATE TABLE submission_attachments (
    id SERIAL PRIMARY KEY,
    submission_id INT NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
)

-- ---------------------------------------- Module quản lý đánh giá khen thưởng ----------------------------------------
-- Biểu mẫu và tiêu chí
-- Bảng biểu mẫu
CREATE TABLE forms (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    activity_id INT,
    criteria_group_id INT,
    target ENUM ('individual', 'group') NOT NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL,
    FOREIGN KEY (criteria_group_id) REFERENCES criteria_groups(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE RESTRICT
)

-- Bảng loại tiêu chí
-- Các loại: dữ liệu hệ thống, họp xét, up minh chứng, ...
CREATE TABLE criterion_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
)

-- Bảng tiêu chí 
CREATE TABLE criteria (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    criterion_type_id INT NOT NULL,
    approval_submission_required BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (criterion_type_id) REFERENCES criterion_types(id) ON DELETE RESTRICT
)

-- Bảng nhóm tiêu chí
-- Toán tử sẽ áp dụng với mọi nhóm tiêu chí con của nó, nếu là nhóm ở nút lá thì sẽ áp dụng với mọi tiêu chí của nó
CREATE TABLE criteria_groups (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    parent_group_id INT,
    operator ENUM('AND', 'OR') DEFAULT 'AND',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bảng điều kiện
CREATE TABLE conditions (
    id SERIAL PRIMARY KEY,
    attribute VARCHAR(100) NOT NULL, -- TODO: tạo bảng attributes
    operator ENUM('=', '!=', '<', '<=', '>', '>=', 'IN', 'NOT IN', 'LIKE') NOT NULL,
    value VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)

-- Bảng liên kết điều kiện và tiêu chí
CREATE TABLE criterion_conditions (
    id SERIAL PRIMARY KEY,
    criterion_id INT NOT NULL,
    condition_id INT NOT NULL,
    FOREIGN KEY (criterion_id) REFERENCES criteria(id) ON DELETE CASCADE,
    FOREIGN KEY (condition_id) REFERENCES conditions(id) ON DELETE CASCADE
)

-- Chấm điểm và bình chọn 
-- Bảng phương thức chấm
-- Các phương thức chấm: 
--      Penalty-based: trừ điểm dựa trên lỗi sai
--      Reward-based: cộng điểm dựa trên thành tích
--      Rubric-based: chấm điểm dựa trên thang điểm (từng mức rõ ràng)
--      Vote: chấm điểm dựa trên bình chọn
-- TODO: Thêm bảng lưu phương thức chấm (cụ thể điểm, mức, thang, ...)
CREATE TABLE scoring_methods (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
)

-- Bảng lưu điểm của tiêu chí
CREATE TABLE criterion_scores (
    id SERIAL PRIMARY KEY,
    criteria_id INT NOT NULL,
    form_id INT NOT NULL,
    scoring_method_id INT NOT NULL,
    max_score DECIMAL(5, 2) NOT NULL,
    min_score DECIMAL(5, 2) NOT NULL,
    is_critical BOOLEAN DEFAULT FALSE, -- Nếu tiêu chí thuộc loại bắt buộc nghĩa là khi tiêu chí ko thỏa mãn thì cả form ko thỏa mãn
    criteria_group_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (criteria_id) REFERENCES criteria(id) ON DELETE CASCADE,
    FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE,
    FOREIGN KEY (scoring_method_id) REFERENCES scoring_methods(id) ON DELETE RESTRICT,
    FOREIGN KEY (criteria_group_id) REFERENCES criteria_groups(id) ON DELETE CASCADE
)

-- Bảng quy đổi điểm (nếu cần, cho điểm rèn luyện là chính)
-- TODO: vì chưa biết có dùng hay ko nên chưa liên kết với bảng nào cả
CREATE TABLE score_mappings (
    id SERIAL PRIMARY KEY,
    min_score DECIMAL(3, 2) NOT NULL,
    max_score DECIMAL(3, 2) NOT NULL,
    converted_score DECIMAL(3, 2) NOT NULL,
    description TEXT
)

-- Bảng lưu bình chọn
-- Các loại vote:
--      Thông qua/ko thông qua = biểu quyết bằng số đông
--      Lưu điểm =  điểm trung bình trong vote
CREATE TABLE votes (
    id SERIAL PRIMARY KEY,
    activity_id INT NOT NULL,
    vote_types ENUM("approval", "score") NOT NULL,
    score DECIMAL(5, 2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
)

-- Khen thưởng và giải thưởng
-- Bảng loại khen thưởng
-- Các loại: 
--      Khen thưởng cấp cơ sở
--      Khen thưởng tháng thi đua
--      Khen thưởng đột xuất
--      Giải thưởng 26/3
--      ...
CREATE TABLE award_categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
);

-- Bảng cấp độ giải thưởng
-- Các loại:
--      Cơ sở
--      Đoàn trường
--      Cấp trường
--      Thành Đoàn
--      Trung ương đoàn
CREATE TABLE award_levels (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    level_index INT NOT NULL UNIQUE,
    description TEXT
)

-- Bảng lưu đợt khen thưởng
CREATE TABLE award_rounds (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    academic_year_id INT NOT NULL,
    semester_id INT,
    application_start_date DATE NOT NULL,
    application_end_date DATE NOT NULL,
    creator_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (academic_year_id) REFERENCES academic_years(id) ON DELETE RESTRICT,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE SET NULL,
    FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE RESTRICT
)

-- Bảng biểu mẫu để chấm điểm cho giải thưởng
CREATE TABLE award_forms (
    id SERIAL PRIMARY KEY,
    form_id INT NOT NULL,
    min_score_required DECIMAL(3, 2),
    details TEXT
)

-- Bảng lưu khen thưởng
CREATE TABLE awards (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    award_round_id INT NOT NULL,
    rank_index INT,
    prize_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (award_round_id) REFERENCES award_rounds(id) ON DELETE CASCADE,
    FOREIGN KEY (prize_id) REFERENCES prizes(id) ON DELETE SET NULL
)

-- Bảng lưu phần thưởng
-- Các loại:
--      Giấy khen
--      Giấy chứng nhận
--      Tiền thưởng
--      ...
CREATE TABLE prizes (
    id SERIAL PRIMARY KEY, 
    name VARCHAR(255) NOT NULL,
    description TEXT
)

-- Hồ sơ và quyết định
-- Bảng lưu thành tựu
CREATE TABLE achievements (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
)

-- Bảng lưu minh chứng người dùng nộp cho tiêu chí
CREATE TABLE criterion_proofs (
    id SERIAL PRIMARY KEY,
    criterion_id INT NOT NULL,
    user_id INT NOT NULL,
    proof_id INT NOT NULL,
    FOREIGN KEY (criterion_id) REFERENCES criteria(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (proof_id) REFERENCES personal_proofs(id) ON DELETE CASCADE
)

-- Bảng hồ sơ xét giải 
CREATE TABLE award_applications (
    id SERIAL PRIMARY KEY,
    award_round_id INT NOT NULL,
    award_category_id INT NOT NULL,
    applicant_user_id INT,
    applicant_org_id INT,
    score DECIMAL(5, 2),
    remarks TEXT,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (award_round_id) REFERENCES award_rounds(id) ON DELETE CASCADE,
    FOREIGN KEY (award_category_id) REFERENCES award_categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (applicant_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (applicant_org_id) REFERENCES organizations(id) ON DELETE CASCADE
)

-- Bảng quyết định trao giải cuối cùng
CREATE TABLE award_final_decisions (
    id SERIAL PRIMARY KEY,
    application_id INT NOT NULL,
    decision_number TEXT,
    decision_date DATETIME,
    granted_by_user_id INT NOT NULL,
    FOREIGN KEY (application_id) REFERENCES award_applications(id) ON DELETE CASCADE,
    FOREIGN KEY (granted_by_user_id) REFERENCES users(id) ON DELETE RESTRICT
)

-- Quản lý minh chứng
-- Bảng loại minh chứng
-- Các loại: 
--      Họp
--      Duyệt minh chứng
--      Xếp loại đoàn viên
--      Chức vụ
--      Giấy khen
--      Giấy chứng nhận/xác nhận tham gia
--      Thời gian nhiệm kỳ
--      Thời gian sinh hoạt
--      Điểm
--      Hệ thống có dữ liệu tham gia
CREATE TABLE proof_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT
)

-- Bảng minh chứng cá nhân
CREATE TABLE personal_proofs (
    id SERIAL PRIMARY KEY,
    user_id INT NOT NULL,
    proof_type_id INT NOT NULL,
    title VARCHAR(255),
    description TEXT,
    issued_date DATETIME, -- Ngày cấp
    issuer_org VARCHAR(255), -- Tổ chức cấp (để như này vì có thể có tổ chức bên ngoài cấp) TODO: fix
    verification_status ENUM("pending", "verified", "rejected") DEFAULT "pending",
    verifier_id INT,
    verified_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (proof_type_id) REFERENCES proof_types(id) ON DELETE RESTRICT,
    FOREIGN KEY (verifier_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Bảng liên kết minh chứng với file minh chứng
CREATE TABLE personal_proof_attachments (
    id SERIAL PRIMARY KEY,
    personal_proof_id INT NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (personal_proof_id) REFERENCES personal_proofs(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE RESTRICT
)



-- TODO

-- ---------------------------------------- Module quản lý thông báo ----------------------------------------

-- ---------------------------------------- Module quản lý thống kê và báo cáo ----------------------------------------