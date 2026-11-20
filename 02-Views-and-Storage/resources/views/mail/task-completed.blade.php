<!doctype html>
<html lang="en"><head><meta charset="utf-8"><title>Task completed</title></head>
<body style="margin:0;padding:32px;background:#f7f6fa;font-family:Arial,sans-serif;color:#28263c;">
<div style="max-width:560px;margin:auto;background:white;border:1px solid #e7e3ee;border-radius:12px;padding:30px;">
<p style="font-size:12px;letter-spacing:2px;color:#6753ac;">TASKS FOR TODAY</p>
<h1 style="font-size:26px;">A task has been completed</h1>
<p><strong>Task:</strong> {{ $task->title }}</p>
<p><strong>Task ID:</strong> {{ $task->id }}</p>
<p><strong>Scheduled date:</strong> {{ $task->task_date->format('F d, Y') }}</p>
<p><strong>Status:</strong> done</p>
<p>This notification was generated because the task's status changed to done.</p>
<p style="font-size:12px;color:#747187;">IT0049 TSA4 &middot; Tasks for Today</p>
</div></body></html>
