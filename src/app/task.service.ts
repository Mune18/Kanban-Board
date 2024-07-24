import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Task } from './task.model';

@Injectable({
  providedIn: 'root'
})
export class TaskService {
  private apiUrl = 'http://localhost/kanban-board2/API/task.php';

  constructor(private http: HttpClient) {}

  getTasks(): Observable<Task[]> {
    return this.http.get<Task[]>(this.apiUrl);
  }

  addTask(task: Task): Observable<any> {
    return this.http.post(this.apiUrl, task);
  }

  updateTask(task: Task): Observable<any> {
    return this.http.put(this.apiUrl, task);
  }

  deleteTask(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}?id=${id}`);
  }

  updateTaskStatus(id: number, status: string): Observable<void> {
    return this.http.put<void>(`${this.apiUrl}/status`, { id, status });
  }
}
