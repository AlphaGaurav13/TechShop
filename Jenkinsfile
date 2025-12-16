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

    }

    post {
        success {
            echo 'CI PASSED ✅'
        }
        failure {
            echo 'CI FAILED ❌'
        }
    }
}
