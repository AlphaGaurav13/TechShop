pipeline {
    agent any

    stages {

        stage('Checkout Code') {
            steps {
                echo 'Fetching code from GitHub'
                checkout scm
            }
        }

        stage('CI Check') {
            steps {
                echo 'Running basic CI checks'
                bat 'echo Code fetched successfully'
            }
        }

        stage('Docker Build') {
            steps {
                echo 'Building Docker image'
                bat 'docker build -t techshop-ci-test .'
            }
        }

    }

    post {
        success {
            echo 'CI + Docker BUILD PASSED ✅'
        }
        failure {
            echo 'CI or Docker BUILD FAILED ❌'
        }
    }
}
