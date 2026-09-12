<?php

require_once "functions.php";

requireLogin();

require_once "fal_api.php";

$id =
    (int)(
        $_GET["id"] ?? 0
    );

$u =
    currentUser();

$st = $conn->prepare(
    "SELECT *
     FROM generations
     WHERE id=? AND user_id=?
     LIMIT 1"
);

$st->bind_param(
    "ii",
    $id,
    $u["id"]
);

$st->execute();

$g =
    $st->get_result()
       ->fetch_assoc();

if (!$g) {
    die("Generation not found.");
}

if (
    $g["request_id"] &&
    !in_array(
        $g["status"],
        ["completed","failed"],
        true
    )
) {

    $s =
        falStatus(
            $g["request_id"]
        );

    if ($s["ok"]) {

        $state =
            $s["data"]["status"]
            ?? "";

        if (
            $state === "IN_QUEUE" ||
            $state === "IN_PROGRESS"
        ) {

            $new =
                $state === "IN_QUEUE"
                ? "queued"
                : "processing";

            $st2 =
                $conn->prepare(
                    "UPDATE generations
                     SET status=?
                     WHERE id=?"
                );

            $st2->bind_param(
                "si",
                $new,
                $id
            );

            $st2->execute();

            $g["status"] =
                $new;

        } elseif (
            $state === "COMPLETED"
        ) {

            $r =
                falResult(
                    $g["request_id"]
                );

            if ($r["ok"]) {

                $video =
                    $r["data"]["video"]["url"]
                    ?? "";

                if ($video !== "") {

                    $st2 =
                        $conn->prepare(
                            "UPDATE generations
                             SET status='completed',
                                 video_url=?
                             WHERE id=?"
                        );

                    $st2->bind_param(
                        "si",
                        $video,
                        $id
                    );

                    $st2->execute();

                    $g["status"] =
                        "completed";

                    $g["video_url"] =
                        $video;

                } else {

                    $msg =
                        "No video URL returned.";

                    $st2 =
                        $conn->prepare(
                            "UPDATE generations
                             SET status='failed',
                                 error_message=?
                             WHERE id=?"
                        );

                    $st2->bind_param(
                        "si",
                        $msg,
                        $id
                    );

                    $st2->execute();

                    $g["status"] =
                        "failed";

                    $g["error_message"] =
                        $msg;
                }

            } else {

                $msg =
                    $r["error"]
                    ?? "Could not fetch result.";

                $st2 =
                    $conn->prepare(
                        "UPDATE generations
                         SET status='failed',
                             error_message=?
                         WHERE id=?"
                    );

                $st2->bind_param(
                    "si",
                    $msg,
                    $id
                );

                $st2->execute();

                $g["status"] =
                    "failed";

                $g["error_message"] =
                    $msg;
            }

        } elseif (
            $state === "FAILED"
        ) {

            $msg =
                "AI generation failed.";

            $st2 =
                $conn->prepare(
                    "UPDATE generations
                     SET status='failed',
                         error_message=?
                     WHERE id=?"
                );

            $st2->bind_param(
                "si",
                $msg,
                $id
            );

            $st2->execute();

            $g["status"] =
                "failed";

            $g["error_message"] =
                $msg;
        }
    }
}
?>