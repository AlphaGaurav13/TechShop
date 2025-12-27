pipeline {
    agent any

    environment {
        // Docker image details
        DOCKER_IMAGE = "gaurious/techshop"
        DOCKER_TAG   = "latest"
        DOCKER_CREDS = "dockerhub-creds"

        // Container names
        WEB_CONTAINER = "techshop_web"
        DB_CONTAINER  = "techshop_db"
        PHPMYADMIN_CONTAINER = "techshop_phpmyadmin"

        // Database config
        MYSQL_ROOT_PASSWORD = "root"
        MYSQL_DATABASE = "techshop_db"
    }

    stages {

        // 1️⃣ Checkout source code from GitHub
        stage('Checkout Code') {
            steps {
                checkout scm
            }
        }

        // 2️⃣ Build Docker image for PHP e-commerce app
        stage('Build Docker Image') {
            steps {
                bat "docker build -t %DOCKER_IMAGE%:%DOCKER_TAG% ."
            }
        }

        // 3️⃣ Login to Docker Hub securely
        stage('Login to Docker Hub') {
            steps {
                withCredentials([usernamePassword(
                    credentialsId: DOCKER_CREDS,
                    usernameVariable: 'DOCKER_USER',
                    passwordVariable: 'DOCKER_PASS'
                )]) {
                    bat "docker login -u %DOCKER_USER% -p %DOCKER_PASS%"
                }
            }
        }

        // 4️⃣ Push image to Docker Hub
        stage('Push Image to Docker Hub') {
            steps {
                bat "docker push %DOCKER_IMAGE%:%DOCKER_TAG%"
            }
        }

        // 5️⃣ Run / Restart MySQL container
        stage('Run MySQL Container') {
            steps {
                bat """
                docker pull mysql:8.0

                docker stop %DB_CONTAINER% || echo DB not running
                docker rm %DB_CONTAINER% || echo DB not present

                docker run -d ^
                --name %DB_CONTAINER% ^
                -e MYSQL_ROOT_PASSWORD=%MYSQL_ROOT_PASSWORD% ^
                -e MYSQL_DATABASE=%MYSQL_DATABASE% ^
                -p 3306:3306 ^
                mysql:8.0
                """
            }
        }

        // 6️⃣ Deploy PHP E-commerce Web Application
        stage('Deploy Web Application') {
            steps {
                bat """
                docker pull %DOCKER_IMAGE%:%DOCKER_TAG%

                docker stop %WEB_CONTAINER% || echo Web not running
                docker rm %WEB_CONTAINER% || echo Web not present

                docker run -d ^
                --name %WEB_CONTAINER% ^
                --link %DB_CONTAINER%:mysql ^
                -p 8090:80 ^
                %DOCKER_IMAGE%:%DOCKER_TAG%
                """
            }
        }

        // 7️⃣ Run phpMyAdmin container
        stage('Run phpMyAdmin') {
            steps {
                bat """
                docker pull phpmyadmin/phpmyadmin

                docker stop %PHPMYADMIN_CONTAINER% || echo phpMyAdmin not running
                docker rm %PHPMYADMIN_CONTAINER% || echo phpMyAdmin not present

                docker run -d ^
                --name %PHPMYADMIN_CONTAINER% ^
                -e PMA_HOST=%DB_CONTAINER% ^
                -e PMA_PORT=3306 ^
                -p 8081:80 ^
                phpmyadmin/phpmyadmin
                """
            }
        }
    }
}
