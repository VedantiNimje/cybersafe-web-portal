let score = 0;

let answered = [false, false, false];

function checkAnswer(question, answer) {

    let result = document.getElementById("result" + question);

    if (answered[question - 1]) {
        return;
    }

    answered[question - 1] = true;

    let correctAnswer;

    if (question === 1) {
        correctAnswer = "phishing";
    }
    else if (question === 2) {
        correctAnswer = "safe";
    }
    else if (question === 3) {
        correctAnswer = "phishing";
    }

    if (answer === correctAnswer) {

        score++;

        result.innerHTML =
            "✅ Correct!";

    } else {

        result.innerHTML =
            "❌ Incorrect. Try to identify the warning signs.";
    }
    
    if (answered[0] && answered[1] && answered[2]) {

        document.getElementById("score").innerHTML =
            "You scored <strong>" + score + " / 3</strong>.";

        if (score === 3) {

            document.getElementById("score").innerHTML +=
                "<br>🎉 Excellent! You have strong phishing awareness.";

        }
        else if (score === 2) {

            document.getElementById("score").innerHTML +=
                "<br>👍 Good job! Keep learning about phishing.";

        }
        else {

            document.getElementById("score").innerHTML +=
                "<br>📚 Keep practicing your cybersecurity awareness.";

        }
    }
}