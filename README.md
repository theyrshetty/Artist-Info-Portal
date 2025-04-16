# Artist Info Portal

This application consists of a musician profile submission form with a complete backend for data management. The system allows musicians to submit their profiles with detailed information about their background, experience, and music samples.

## Prerequisites

- WAMP Server (Windows, Apache, MySQL, PHP)
- PHP 7.0 or higher
- MySQL 5.6 or higher

## Installation

1. **Set up WAMP Server**
   - Download and install WAMP Server from [wampserver.com](https://www.wampserver.com/en/)
   - Start the WAMP server services

2. **Project Setup**
   - Clone or download this repository
   - Place the files in your WAMP server's `www` directory (typically `C:\wamp64\www\musician-profile\`)

3. **Database Configuration**
   - Open phpMyAdmin (typically at http://localhost/phpmyadmin/)
   - Create a new database named `musician_profiles`
   - The application will automatically create the required table when first accessed

## Files Structure

- `index.html` - The frontend form for musician profile submission
- `index.php` - Backend PHP script that handles form processing and database operations

## Features

- **Comprehensive Form**
  - Personal Information (name, age, email)
  - Musical Background (professional status, instruments, experience)
  - Performance Details (skill level, upcoming performances, website)
  - Audio Demo Upload

- **Interactive UI**
  - Real-time form validation
  - Progress indicator
  - Success/error messages

- **Full CRUD Operations**
  - Create new musician profiles
  - Read existing profiles
  - Update musician information
  - Delete profiles

- **File Management**
  - Upload and store audio demos in MP3/WAV format
  - File validation for size and format

## Usage

1. **Access the Form**
   - Navigate to `http://localhost/musician-profile/` in your web browser

2. **Submit a New Profile**
   - Fill out the required fields in the form
   - Upload an audio demo file (MP3 or WAV format, max 10MB)
   - Click "Submit Profile"

3. **View Existing Profiles**
   - Navigate to `http://localhost/musician-profile/musician_profile.php?action=list`
   - View all submitted profiles in a table format

4. **Edit or Delete Profiles**
   - From the listing page, click "Edit" to modify a profile
   - Click "Delete" to remove a profile (requires confirmation)

## Form Fields

| Field | Description | Required |
|-------|-------------|----------|
| Full Name | Musician's full name | Yes |
| Age | Age in years | Yes |
| Email | Contact email address | Yes |
| Professional Status | Whether they are a professional musician | Yes |
| Primary Instrument | Main instrument played | Yes |
| Secondary Instruments | Other instruments the musician plays | No |
| Years of Experience | How long they've been playing music | Yes |
| Experience Description | Detailed information about their musical background | Yes |
| Skill Level | Self-rated proficiency (1-10) | Yes |
| Next Performance | Date of upcoming performance | No |
| Website | Link to musician's website or profile | Yes |
| Demo | Audio file showcasing their work | Yes |

## Security Features

- Input validation on both client and server side
- SQL injection prevention through proper escaping
- File upload validation and restrictions

## Troubleshooting

- **Database Connection Issues**
  - Verify WAMP server is running
  - Check that MySQL service is active
  - Confirm database name is `musician_profiles`

- **File Upload Problems**
  - Ensure the `uploads` directory has write permissions
  - Check that the file size is under 10MB
  - Verify the file format is MP3 or WAV

- **Form Submission Errors**
  - Make sure all required fields are completed
  - Check that the form action points to `musician_profile.php`
  - Verify the form's `enctype` is set to `multipart/form-data`

## License

This project is open-source and available for personal and commercial use.
