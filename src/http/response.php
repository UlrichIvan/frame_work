<?php

namespace App\Http;

use App\interface\ResponseInterface;

/**
 * Class Response to send response from entry request
 */
class Response implements ResponseInterface
{
      private int $status;
      private string $statusText;

      public function setStatus(int $status): ?self
      {
            try {
                  if (!in_array($status, Response::HTTP_CODE_RESPONSES)) {
                        throw new \Exception("Invalid status code send $status", 1);
                  }
                  $this->status = $status;
                  http_response_code($status);
                  return $this;
            } catch (\Throwable $th) {
                  throw $th;
            }
      }

      public function getStatus(): ?int
      {
            return !empty($this->status) ? $this->status : null;
      }

      public function setStatusText(int $statusText = null): self
      {
            $this->statusText = $statusText;
            return $this;
      }

      public function getStatusText(): ?string
      {
            return !empty($this->statusText) ? $this->statusText : null;
      }

      public function json(mixed $data): ?self
      {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($data);
            $this->close();
            return $this;
      }

      public function close(): void
      {
            exit;
      }
}
