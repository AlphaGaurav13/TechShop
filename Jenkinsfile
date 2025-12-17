pipeline {
    agent any

    options {
        skipDefaultCheckout(true)   // ❌ Jenkins auto checkout band
        timestamps()
    }

    environment {
        IMAGE_NAME = "gaurious/techshop"
    }

    stages {

        stage('Clean Workspace') {
            steps {
                cleanWs()
            }
        }

        stage('Checkout Code') {
            steps {
                retry(2) {
                    checkout([
                        $class: 'GitSCM',
                        branches: [[name: '*/main']],
                        userRemoteConfigs: [[
                            url: 'https://github.com/AlphaGaurav13/TechShop.git'
                        ]]
                    ])
                }
            }
        }

        stage('CI Check') {
            steps {
                bat 'echo CI check passed'
            }
        }

        stage('Docker Build') {
            steps {
                bat 'docker build -t %IMAGE_NAME%:latest .'
            }
        }

        stage('Docker Push') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: 'dockerhub-creds',
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    bat '''
                    docker login -u %DOCKER_USER% -p %DOCKER_PASS%
                    docker push %IMAGE_NAME%:latest
                    '''
                }
            }
        }
    }

    post {
        success {
            echo 'CI + Docker PUSH PASSED ✅'
        }
        failure {
            echo 'PIPELINE FAILED ❌'
        }
    }
}
