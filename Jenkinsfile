pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                checkout scm
            }
        }

        stage('Deploy to XAMPP') {
            steps {
                bat '''
                echo Deploying to XAMPP...
                if not exist C:\\xampp\\htdocs\\techshop mkdir C:\\xampp\\htdocs\\techshop
                xcopy /E /I /Y * C:\\xampp\\htdocs\\techshop
                '''
            }
        }
    }
}
